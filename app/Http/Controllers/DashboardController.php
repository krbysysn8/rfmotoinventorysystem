<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        // Cache categories and suppliers for 5 minutes (rarely change)
        $totalCategories = Cache::remember('total_categories', 300, fn() =>
            DB::table('categories')->count()
        );

        $totalSuppliers = Cache::remember('total_suppliers', 300, fn() =>
            DB::table('suppliers')->where('status', 'active')->count()
        );

        // Fetch all active products in ONE query with category eager loaded
        // Cache for 60 seconds
        $products = Cache::remember('active_products', 60, fn() =>
            Product::where('is_active', true)->with('category')->get()
        );

        // Compute everything from the in-memory collection (no extra queries)
        $totalProducts = $products->count();
        $totalStock    = $products->sum('stock_qty');
        $lowStock      = $products->filter(fn($p) => $p->stock_qty <= $p->reorder_level)->count();
        $outOfStock    = $products->filter(fn($p) => $p->stock_qty == 0)->count();

        $forecast = $products->sortByDesc('stock_qty')
            ->take(8)
            ->map(fn($p) => [
                'sku'          => $p->sku,
                'name'         => $p->product_name,
                'stock'        => $p->stock_qty,
                'reorder'      => $p->reorder_level,
                'stock_status' => $p->stock_status,
            ])->values();

        $alerts = $products
            ->filter(fn($p) => $p->stock_qty <= $p->reorder_level)
            ->sortBy('stock_qty')
            ->map(fn($p) => [
                'id'       => $p->product_id,
                'sku'      => $p->sku,
                'name'     => $p->product_name,
                'category' => $p->category?->category_name,
                'stock'    => $p->stock_qty,
                'reorder'  => $p->reorder_level,
            ])->values();

        // Sales data — cache for 30 seconds (more realtime)
        $todaySales = Cache::remember('today_sales', 30, fn() =>
            SalesOrder::where('status', 'completed')
                ->whereDate('order_date', today())
                ->get()
        );

        $recentSales = Cache::remember('recent_sales', 30, fn() =>
            SalesOrder::with(['items', 'servedBy'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(fn($o) => [
                    'order_number'  => $o->order_number,
                    'customer_name' => $o->customer_name,
                    'items_count'   => $o->items->count(),
                    'total_amount'  => $o->total_amount,
                    'served_by'     => $o->servedBy?->username,
                    'order_date'    => $o->order_date->toDateString(),
                    'status'        => $o->status,
                ])
        );

        // Stock movements — cache for 30 seconds
        $recentMovements = Cache::remember('recent_movements', 30, fn() =>
            DB::table('stock_movements as sm')
                ->join('products as p', 'sm.product_id', '=', 'p.product_id')
                ->leftJoin('users as u', 'sm.performed_by', '=', 'u.user_id')
                ->select(
                    'p.product_name',
                    'p.sku',
                    'sm.movement_type',
                    'sm.quantity',
                    'sm.movement_date',
                    'sm.created_at',
                    DB::raw("COALESCE(u.full_name, u.username) as performed_by_name")
                )
                ->orderByDesc('sm.created_at')
                ->limit(8)
                ->get()
        );

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_products'   => $totalProducts,
                'total_stock'      => $totalStock,
                'low_stock_count'  => $lowStock,
                'out_of_stock'     => $outOfStock,
                'total_categories' => $totalCategories,
                'total_suppliers'  => $totalSuppliers,
                'sales_today'      => $todaySales->count(),
                'sales_today_amt'  => $todaySales->sum('total_amount'),
                'forecast'         => $forecast,
                'low_stock_alerts' => $alerts,
                'recent_sales'     => $recentSales,
                'recent_movements' => $recentMovements,
            ],
        ]);
    }
}

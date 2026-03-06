<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\VerifyAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalStock    = Product::where('is_active', true)->sum('stock_qty');

        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock_qty', '<=', 'reorder_level')
            ->count();

        $todaySales = SalesOrder::where('status', 'completed')
            ->whereDate('order_date', today())
            ->get();

        $pendingVerify = VerifyAction::where('status', 'pending')->count();

        $forecast = Product::with('category')
            ->where('is_active', true)
            ->orderByDesc('stock_qty')
            ->take(8)
            ->get()
            ->map(fn($p) => [
                'sku'          => $p->sku,
                'name'         => $p->product_name,
                'stock'        => $p->stock_qty,
                'reorder'      => $p->reorder_level,
                'stock_status' => $p->stock_status,
            ]);

        $alerts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock_qty', '<=', 'reorder_level')
            ->orderBy('stock_qty')
            ->get()
            ->map(fn($p) => [
                'id'       => $p->product_id,
                'sku'      => $p->sku,
                'name'     => $p->product_name,
                'category' => $p->category?->category_name,
                'stock'    => $p->stock_qty,
                'reorder'  => $p->reorder_level,
            ]);

        $recentSales = SalesOrder::with(['items', 'servedBy'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($o) => [
                'order_number'   => $o->order_number,
                'customer_name'  => $o->customer_name,
                'items_count'    => $o->items->count(),
                'total_amount'   => $o->total_amount,
                'served_by'      => $o->servedBy?->username,
                'order_date'     => $o->order_date->toDateString(),
                'status'         => $o->status,
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_products'  => $totalProducts,
                'total_stock'     => $totalStock,
                'low_stock_count' => $lowStock,
                'sales_today'     => $todaySales->count(),
                'sales_today_amt' => $todaySales->sum('total_amount'),
                'pending_verify'  => $pendingVerify,
                'forecast'        => $forecast,
                'low_stock_alerts'=> $alerts,
                'recent_sales'    => $recentSales,
            ],
        ]);
    }
}
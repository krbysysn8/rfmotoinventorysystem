<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        // ── Counters (cached 60s, busted on change) ───────────────────────────
        $totalCategories = Cache::remember('total_categories', 60, fn() =>
            DB::table('categories')->count()
        );
        $totalSuppliers = Cache::remember('total_suppliers', 60, fn() =>
            DB::table('suppliers')->where('status', 'active')->count()
        );

        // ── Stock stats via two lean raw queries + 10s cache ──────────────────
        // Raw DB queries (no Eloquent model loading) = much faster
        // 10s cache = near-realtime but avoids hammering DB on every page load
        [$totalProducts, $totalStock, $lowStock, $outOfStock, $forecast, $alerts] =
            Cache::remember('dashboard_stock_stats', 10, function () {

            $products = DB::table('products as p')
                ->join('categories as c', 'p.category_id', '=', 'c.category_id')
                ->where('p.is_active', true)
                ->select('p.product_id', 'p.sku', 'p.product_name', 'p.stock_qty', 'p.reorder_level', 'c.category_name')
                ->get()
                ->keyBy('product_id');

            $variations = DB::table('product_variations')
                ->whereIn('product_id', $products->keys())
                ->where('is_active', true)
                ->select('variation_id', 'product_id', 'variation_name', 'sku', 'stock_qty')
                ->get()
                ->groupBy('product_id');

            // Flatten: each variation = its own item; product with no variations = 1 item
            $allItems = collect();
            foreach ($products as $p) {
                $vars         = $variations->get($p->product_id, collect());
                $reorderLevel = (int)$p->reorder_level > 0 ? (int)$p->reorder_level : 5;

                if ($vars->isNotEmpty()) {
                    foreach ($vars as $v) {
                        $stock = (int)$v->stock_qty;
                        $allItems->push((object)[
                            'product_id'    => $p->product_id,
                            'sku'           => $v->sku ?: $p->sku,
                            'product_name'  => $p->product_name . ' — ' . $v->variation_name,
                            'stock_qty'     => $stock,
                            'reorder_level' => $reorderLevel,
                            'stock_status'  => $stock == 0 ? 'out_of_stock' : ($stock <= $reorderLevel ? 'low_stock' : 'in_stock'),
                            'category_name' => $p->category_name,
                        ]);
                    }
                } else {
                    $stock = (int)$p->stock_qty;
                    $allItems->push((object)[
                        'product_id'    => $p->product_id,
                        'sku'           => $p->sku,
                        'product_name'  => $p->product_name,
                        'stock_qty'     => $stock,
                        'reorder_level' => $reorderLevel,
                        'stock_status'  => $stock == 0 ? 'out_of_stock' : ($stock <= $reorderLevel ? 'low_stock' : 'in_stock'),
                        'category_name' => $p->category_name,
                    ]);
                }
            }

            $totalProducts = $allItems->count();
            $totalStock    = $allItems->sum('stock_qty');
            $lowStock      = $allItems->filter(fn($i) => $i->stock_qty > 0 && $i->stock_qty <= $i->reorder_level)->count();
            $outOfStock    = $allItems->filter(fn($i) => $i->stock_qty == 0)->count();

            $forecast = $allItems->sortByDesc('stock_qty')
                ->take(8)
                ->map(fn($i) => [
                    'sku'          => $i->sku,
                    'name'         => $i->product_name,
                    'stock'        => $i->stock_qty,
                    'reorder'      => $i->reorder_level,
                    'stock_status' => $i->stock_status,
                ])->values();

            $alerts = $allItems
                ->filter(fn($i) => $i->stock_qty > 0 && $i->stock_qty <= $i->reorder_level)
                ->sortBy('stock_qty')
                ->map(fn($i) => [
                    'id'       => $i->product_id,
                    'sku'      => $i->sku,
                    'name'     => $i->product_name,
                    'category' => $i->category_name,
                    'stock'    => $i->stock_qty,
                    'reorder'  => $i->reorder_level,
                ])->values();

            return [$totalProducts, $totalStock, $lowStock, $outOfStock, $forecast, $alerts];
        });

        // ── Sales (cached 30s) ────────────────────────────────────────────────
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

        // ── Recent stock movements (cached 30s) ───────────────────────────────
        $recentMovements = Cache::remember('recent_movements', 30, fn() =>
            DB::table('stock_movements as sm')
                ->join('products as p', 'sm.product_id', '=', 'p.product_id')
                ->leftJoin('product_variations as pv', 'sm.variation_id', '=', 'pv.variation_id')
                ->leftJoin('users as u', 'sm.performed_by', '=', 'u.user_id')
                ->select(
                    DB::raw("CASE WHEN pv.variation_name IS NOT NULL THEN p.product_name || ' - ' || pv.variation_name ELSE p.product_name END as product_name"),
                    DB::raw("COALESCE(pv.sku, p.sku) as sku"),
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

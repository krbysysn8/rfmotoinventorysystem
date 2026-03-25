<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // ─────────────────────────────────────────────────────────
    //  GET /reports  — blade view
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        return view('reports');
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/inventory-summary
    //  Category breakdown: totals, in/low/out stock, value
    // ─────────────────────────────────────────────────────────
    public function inventorySummary(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        // Get all active products with their variations
        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->select(
                'p.product_id',
                'c.category_id',
                'c.category_name',
                'p.stock_qty',
                'p.reorder_level',
                'p.unit_price',
                'p.updated_at'
            )
            ->where('p.is_active', true);

        if ($from) $query->whereDate('p.updated_at', '>=', $from);
        if ($to)   $query->whereDate('p.updated_at', '<=', $to);

        $products = $query->get();

        // Get all active variations for these products
        $productIds = $products->pluck('product_id');
        $variations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->select('product_id', 'stock_qty')
            ->get()
            ->groupBy('product_id');

        // Build per-category rows using effective stock
        $catMap = [];
        foreach ($products as $p) {
            $catId   = $p->category_id;
            $catName = $p->category_name;
            $vars    = $variations->get($p->product_id, collect());

            if (!isset($catMap[$catId])) {
                $catMap[$catId] = [
                    'category_name' => $catName,
                    'total_items'   => 0,
                    'in_stock'      => 0,
                    'low_stock'     => 0,
                    'out_of_stock'  => 0,
                    'total_value'   => 0,
                ];
            }

            if ($vars->isNotEmpty()) {
                // Count each variation as a separate item
                foreach ($vars as $v) {
                    $catMap[$catId]['total_items']++;
                    $stock = (int)$v->stock_qty;
                    if ($stock === 0)                          $catMap[$catId]['out_of_stock']++;
                    elseif ($stock <= $p->reorder_level)      $catMap[$catId]['low_stock']++;
                    else                                       $catMap[$catId]['in_stock']++;
                    $catMap[$catId]['total_value'] += $stock * (float)$p->unit_price;
                }
            } else {
                // No variations — count product itself
                $catMap[$catId]['total_items']++;
                $stock = (int)$p->stock_qty;
                if ($stock === 0)                          $catMap[$catId]['out_of_stock']++;
                elseif ($stock <= $p->reorder_level)      $catMap[$catId]['low_stock']++;
                else                                       $catMap[$catId]['in_stock']++;
                $catMap[$catId]['total_value'] += $stock * (float)$p->unit_price;
            }
        }

        $rows = collect(array_values($catMap))->sortBy('category_name')->values();

        // Totals row
        $totals = [
            'category_name' => 'TOTAL',
            'total_items'   => $rows->sum('total_items'),
            'in_stock'      => $rows->sum('in_stock'),
            'low_stock'     => $rows->sum('low_stock'),
            'out_of_stock'  => $rows->sum('out_of_stock'),
            'total_value'   => $rows->sum('total_value'),
        ];

        return response()->json([
            'status'  => 'success',
            'summary' => $rows,
            'totals'  => $totals,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/stock-movement
    //  Monthly stock in/out for the last 6 months
    // ─────────────────────────────────────────────────────────
    public function stockMovement(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        // Build month list between from and to (max 12 months, default last 6)
        $dateFrom = $from ? \Carbon\Carbon::parse($from)->startOfMonth() : now()->subMonths(5)->startOfMonth();
        $dateTo   = $to   ? \Carbon\Carbon::parse($to)->startOfMonth()   : now()->startOfMonth();

        // Cap at 12 months to avoid huge ranges
        if ($dateFrom->diffInMonths($dateTo) > 11) {
            $dateFrom = $dateTo->copy()->subMonths(11);
        }

        $months = collect();
        $cursor = $dateFrom->copy();
        while ($cursor->lte($dateTo)) {
            $months->push($cursor->format('Y-m'));
            $cursor->addMonth();
        }

        $rows = DB::table('stock_movements')
            ->selectRaw("TO_CHAR(movement_date, 'YYYY-MM') as month")
            ->selectRaw('movement_type')
            ->selectRaw('SUM(quantity) as total')
            ->whereIn(
                DB::raw("TO_CHAR(movement_date, 'YYYY-MM')"),
                $months->toArray()
            )
            ->groupByRaw("TO_CHAR(movement_date, 'YYYY-MM'), movement_type")
            ->orderByRaw("TO_CHAR(movement_date, 'YYYY-MM')")
            ->get();

        $stockIn  = [];
        $stockOut = [];

        foreach ($months as $m) {
            $inRow  = $rows->where('month', $m)->where('movement_type', 'in')->first();
            $outRow = $rows->where('month', $m)->where('movement_type', 'out')->first();
            $stockIn[]  = $inRow  ? (int)$inRow->total  : 0;
            $stockOut[] = $outRow ? (int)$outRow->total : 0;
        }

        return response()->json([
            'status'    => 'success',
            'labels'    => $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
            'stock_in'  => $stockIn,
            'stock_out' => $stockOut,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/low-stock
    //  Items at or below reorder level — variation-aware
    // ─────────────────────────────────────────────────────────
    public function lowStock(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->select(
                'p.product_id', 'p.sku', 'p.barcode', 'p.product_name',
                'p.stock_qty', 'p.reorder_level', 'p.unit_price', 'p.updated_at',
                'c.category_name',
                DB::raw("COALESCE(s.supplier_name, '—') as supplier_name")
            )
            ->where('p.is_active', true);

        if ($from) $query->whereDate('p.updated_at', '>=', $from);
        if ($to)   $query->whereDate('p.updated_at', '<=', $to);

        $products = $query->get();

        $productIds = $products->pluck('product_id');
        $variations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->select('product_id', 'variation_name', 'sku', 'barcode', 'stock_qty')
            ->get()
            ->groupBy('product_id');

        $items = collect();
        foreach ($products as $p) {
            $reorderLevel = (int)$p->reorder_level > 0 ? (int)$p->reorder_level : 5;
            $vars = $variations->get($p->product_id, collect());

            if ($vars->isNotEmpty()) {
                foreach ($vars as $v) {
                    $stock = (int)$v->stock_qty;
                    if ($stock > 0 && $stock <= $reorderLevel) {
                        $items->push((object)[
                            'product_id'    => $p->product_id,
                            'sku'           => $v->sku ?: $p->sku,
                            'barcode'       => $v->barcode ?: $p->barcode,
                            'product_name'  => $p->product_name . ' — ' . $v->variation_name,
                            'stock_qty'     => $stock,
                            'reorder_level' => $reorderLevel,
                            'shortage'      => $reorderLevel - $stock,
                            'category_name' => $p->category_name,
                            'supplier_name' => $p->supplier_name,
                        ]);
                    }
                }
            } else {
                $stock = (int)$p->stock_qty;
                if ($stock > 0 && $stock <= $reorderLevel) {
                    $items->push((object)[
                        'product_id'    => $p->product_id,
                        'sku'           => $p->sku,
                        'barcode'       => $p->barcode,
                        'product_name'  => $p->product_name,
                        'stock_qty'     => $stock,
                        'reorder_level' => $reorderLevel,
                        'shortage'      => $reorderLevel - $stock,
                        'category_name' => $p->category_name,
                        'supplier_name' => $p->supplier_name,
                    ]);
                }
            }
        }

        $sorted = $items->sortBy('stock_qty')->values();

        return response()->json([
            'status' => 'success',
            'items'  => $sorted,
            'count'  => $sorted->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/out-of-stock
    //  Items with zero stock — variation-aware
    // ─────────────────────────────────────────────────────────
    public function outOfStock(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->select(
                'p.product_id', 'p.sku', 'p.barcode', 'p.product_name',
                'p.unit_price', 'p.reorder_level', 'p.updated_at',
                'c.category_name',
                DB::raw("COALESCE(s.supplier_name, '—') as supplier_name")
            )
            ->where('p.is_active', true);

        if ($from) $query->whereDate('p.updated_at', '>=', $from);
        if ($to)   $query->whereDate('p.updated_at', '<=', $to);

        $products = $query->get();

        $productIds = $products->pluck('product_id');
        $variations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->select('product_id', 'variation_name', 'sku', 'barcode', 'stock_qty')
            ->get()
            ->groupBy('product_id');

        $items = collect();
        foreach ($products as $p) {
            $vars = $variations->get($p->product_id, collect());

            if ($vars->isNotEmpty()) {
                foreach ($vars as $v) {
                    if ((int)$v->stock_qty === 0) {
                        $items->push((object)[
                            'product_id'    => $p->product_id,
                            'sku'           => $v->sku ?: $p->sku,
                            'barcode'       => $v->barcode ?: $p->barcode,
                            'product_name'  => $p->product_name . ' — ' . $v->variation_name,
                            'unit_price'    => $p->unit_price,
                            'reorder_level' => $p->reorder_level,
                            'updated_at'    => $p->updated_at,
                            'category_name' => $p->category_name,
                            'supplier_name' => $p->supplier_name,
                        ]);
                    }
                }
            } else {
                if ((int)$p->stock_qty === 0) {
                    $items->push((object)[
                        'product_id'    => $p->product_id,
                        'sku'           => $p->sku,
                        'barcode'       => $p->barcode,
                        'product_name'  => $p->product_name,
                        'unit_price'    => $p->unit_price,
                        'reorder_level' => $p->reorder_level,
                        'updated_at'    => $p->updated_at,
                        'category_name' => $p->category_name,
                        'supplier_name' => $p->supplier_name,
                    ]);
                }
            }
        }

        $sorted = $items->sortByDesc('updated_at')->values();

        return response()->json([
            'status' => 'success',
            'items'  => $sorted,
            'count'  => $sorted->count(),
        ]);
    }

    //  GET /api/reports/supplier-report
    //  Items per supplier + supplier status
    // ─────────────────────────────────────────────────────────
    public function supplierReport(Request $request): JsonResponse
    {
        $suppliers = DB::table('suppliers as s')
            ->leftJoin('products as p', function ($join) {
                $join->on('p.supplier_id', '=', 's.supplier_id')
                     ->where('p.is_active', true);
            })
            ->select(
                's.supplier_id',
                's.supplier_name',
                's.status',
                DB::raw('COUNT(p.product_id) as item_count')
            )
            ->groupBy('s.supplier_id', 's.supplier_name', 's.status')
            ->orderBy('item_count', 'desc')
            ->get();

        return response()->json([
            'status'    => 'success',
            'suppliers' => $suppliers,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/sales-summary
    //  Sales totals for the date range (for future use)
    // ─────────────────────────────────────────────────────────
    public function salesSummary(Request $request): JsonResponse
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to   = $request->query('to',   now()->toDateString());

        $totals = DB::table('sales_orders')
            ->whereBetween('order_date', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(total_amount) as total_revenue')
            ->selectRaw('SUM(discount) as total_discounts')
            ->first();

        $daily = DB::table('sales_orders')
            ->whereBetween('order_date', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('order_date')
            ->selectRaw('COUNT(*) as orders')
            ->selectRaw('SUM(total_amount) as revenue')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        return response()->json([
            'status' => 'success',
            'totals' => $totals,
            'daily'  => $daily,
        ]);
    }
}

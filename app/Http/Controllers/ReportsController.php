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
    // ─────────────────────────────────────────────────────────
    public function inventorySummary(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

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

        $products   = $query->get();
        $productIds = $products->pluck('product_id');
        $variations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->select('product_id', 'stock_qty')
            ->get()
            ->groupBy('product_id');

        $catMap = [];
        foreach ($products as $p) {
            $catId   = $p->category_id;
            $catName = $p->category_name;
            $vars    = $variations->get($p->product_id, collect());

            if (!isset($catMap[$catId])) {
                $catMap[$catId] = [
                    'category_name' => $catName,
                    'total_items'   => 0,
                    'total_stock'   => 0,  // total units across all variations
                    'in_stock'      => 0,
                    'low_stock'     => 0,
                    'out_of_stock'  => 0,
                    'total_value'   => 0,
                ];
            }

            if ($vars->isNotEmpty()) {
                foreach ($vars as $v) {
                    $catMap[$catId]['total_items']++;
                    $stock = (int)$v->stock_qty;
                    $catMap[$catId]['total_stock'] += $stock;
                    if ($stock === 0)                     $catMap[$catId]['out_of_stock']++;
                    elseif ($stock <= $p->reorder_level) $catMap[$catId]['low_stock']++;
                    else                                  $catMap[$catId]['in_stock']++;
                    $catMap[$catId]['total_value'] += $stock * (float)$p->unit_price;
                }
            } else {
                $catMap[$catId]['total_items']++;
                $stock = (int)$p->stock_qty;
                $catMap[$catId]['total_stock'] += $stock;
                if ($stock === 0)                     $catMap[$catId]['out_of_stock']++;
                elseif ($stock <= $p->reorder_level) $catMap[$catId]['low_stock']++;
                else                                  $catMap[$catId]['in_stock']++;
                $catMap[$catId]['total_value'] += $stock * (float)$p->unit_price;
            }
        }

        $rows   = collect(array_values($catMap))->sortBy('category_name')->values();
        $totals = [
            'category_name' => 'TOTAL',
            'total_items'   => $rows->sum('total_items'),
            'total_stock'   => $rows->sum('total_stock'),
            'in_stock'      => $rows->sum('in_stock'),
            'low_stock'     => $rows->sum('low_stock'),
            'out_of_stock'  => $rows->sum('out_of_stock'),
            'total_value'   => $rows->sum('total_value'),
        ];

        return response()->json(['status' => 'success', 'summary' => $rows, 'totals' => $totals]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/stock-movement
    // ─────────────────────────────────────────────────────────
    public function stockMovement(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $dateFrom = $from ? \Carbon\Carbon::parse($from)->startOfMonth() : now()->subMonths(5)->startOfMonth();
        $dateTo   = $to   ? \Carbon\Carbon::parse($to)->startOfMonth()   : now()->startOfMonth();

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
            ->whereIn(DB::raw("TO_CHAR(movement_date, 'YYYY-MM')"), $months->toArray())
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

        $products   = $query->get();
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
        return response()->json(['status' => 'success', 'items' => $sorted, 'count' => $sorted->count()]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/out-of-stock
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

        $products   = $query->get();
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
        return response()->json(['status' => 'success', 'items' => $sorted, 'count' => $sorted->count()]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/supplier-report
    // ─────────────────────────────────────────────────────────
    public function supplierReport(Request $request): JsonResponse
    {
        $suppliers = DB::table('suppliers as s')
            ->leftJoin('products as p', function ($join) {
                $join->on('p.supplier_id', '=', 's.supplier_id')->where('p.is_active', true);
            })
            ->select('s.supplier_id', 's.supplier_name', 's.status', DB::raw('COUNT(p.product_id) as item_count'))
            ->groupBy('s.supplier_id', 's.supplier_name', 's.status')
            ->orderBy('item_count', 'desc')
            ->get();

        return response()->json(['status' => 'success', 'suppliers' => $suppliers]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/sales-summary
    //  Full sales report with returns impact, filterable by month
    // ─────────────────────────────────────────────────────────
    public function salesSummary(Request $request): JsonResponse
    {
        // Default: current month
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to   = $request->query('to',   now()->toDateString());

        // ── Sales totals ──────────────────────────────────────
        // sales_orders.total_amount is ALREADY the post-return revenue
        // because ReturnController deducts refund_amount from it on every return.
        // So we just sum it directly — no further subtraction needed.
        $salesTotals = DB::table('sales_orders')
            ->whereBetween('order_date', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        // ── Returns totals for the same period ───────────────
        $returnTotals = DB::table('return_requests')
            ->whereBetween('return_date', [$from, $to])
            ->selectRaw('COUNT(*) as total_returns')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as total_refunds')
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_returned_qty')
            ->first();

        // ── Daily breakdown ───────────────────────────────────
        $dailySales = DB::table('sales_orders')
            ->whereBetween('order_date', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('order_date::date as sale_date')
            ->selectRaw('COUNT(*) as orders')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue')
            ->groupBy(DB::raw('order_date::date'))
            ->orderBy(DB::raw('order_date::date'))
            ->get()
            ->keyBy('sale_date');

        $dailyReturns = DB::table('return_requests')
            ->whereBetween('return_date', [$from, $to])
            ->selectRaw('return_date::date as return_date')
            ->selectRaw('COUNT(*) as return_count')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refunds')
            ->selectRaw('COALESCE(SUM(quantity), 0) as returned_qty')
            ->groupBy(DB::raw('return_date::date'))
            ->orderBy(DB::raw('return_date::date'))
            ->get()
            ->keyBy('return_date');

        // Include every date that had sales OR returns
        $allDates = collect($dailySales->keys())->merge($dailyReturns->keys())->unique()->sort()->values();

        $daily = $allDates->map(function ($date) use ($dailySales, $dailyReturns) {
            $s       = $dailySales->get($date);
            $r       = $dailyReturns->get($date);
            $gross   = $s ? (float)$s->revenue  : 0;
            $refunds = $r ? (float)$r->refunds  : 0;
            return [
                'date'         => $date,
                'orders'       => $s ? (int)$s->orders       : 0,
                'revenue'      => max(0, $gross - $refunds),  // actual per-day revenue after refunds
                'return_count' => $r ? (int)$r->return_count : 0,
                'refunds'      => $refunds,
                'returned_qty' => $r ? (int)$r->returned_qty : 0,
            ];
        })->values();

        // ── Top-selling products for the period ───────────────
        $topProducts = DB::table('sales_order_items as soi')
            ->join('sales_orders as so', 'soi.order_id', '=', 'so.order_id')
            ->join('products as p', 'soi.product_id', '=', 'p.product_id')
            ->whereBetween('so.order_date', [$from, $to])
            ->where('so.status', 'completed')
            ->selectRaw('p.product_name')
            ->selectRaw('p.sku')
            ->selectRaw('SUM(soi.quantity) as total_qty')
            ->selectRaw('SUM(soi.subtotal) as total_revenue')
            ->groupBy('p.product_id', 'p.product_name', 'p.sku')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // ── Monthly chart — same date range as the filter ────────
        // Group by month within the selected from/to range
        $chartFrom = \Carbon\Carbon::parse($from)->startOfMonth();
        $chartTo   = \Carbon\Carbon::parse($to)->endOfMonth();

        $months = collect();
        $cursor = $chartFrom->copy();
        while ($cursor->lte($chartTo)) {
            $months->push($cursor->format('Y-m'));
            $cursor->addMonth();
        }

        $monthlySales = DB::table('sales_orders')
            ->where('status', 'completed')
            ->whereDate('order_date', '>=', $from)
            ->whereDate('order_date', '<=', $to)
            ->selectRaw("TO_CHAR(order_date, 'YYYY-MM') as month")
            ->selectRaw('COALESCE(SUM(total_amount), 0) as gross_revenue')
            ->groupByRaw("TO_CHAR(order_date, 'YYYY-MM')")
            ->get()->keyBy('month');

        $monthlyRefunds = DB::table('return_requests')
            ->whereDate('return_date', '>=', $from)
            ->whereDate('return_date', '<=', $to)
            ->selectRaw("TO_CHAR(return_date, 'YYYY-MM') as month")
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refunds')
            ->groupByRaw("TO_CHAR(return_date, 'YYYY-MM')")
            ->get()->keyBy('month');

        $chartRevenue = [];
        foreach ($months as $m) {
            $gross   = isset($monthlySales[$m])   ? (float)$monthlySales[$m]->gross_revenue : 0;
            $refunds = isset($monthlyRefunds[$m]) ? (float)$monthlyRefunds[$m]->refunds     : 0;
            $chartRevenue[] = max(0, $gross - $refunds);
        }

        $grossRevenue = (float)$salesTotals->total_revenue;
        $totalRefunds = (float)$returnTotals->total_refunds;

        return response()->json([
            'status'  => 'success',
            'period'  => ['from' => $from, 'to' => $to],
            'totals'  => [
                'total_orders'       => (int)$salesTotals->total_orders,
                'total_revenue'      => max(0, $grossRevenue - $totalRefunds), // actual revenue after refunds
                'total_returns'      => (int)$returnTotals->total_returns,
                'total_refunds'      => $totalRefunds,
                'total_returned_qty' => (int)$returnTotals->total_returned_qty,
            ],
            'daily'        => $daily,
            'top_products' => $topProducts,
            'chart' => [
                'labels'  => $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
                'revenue' => $chartRevenue,
            ],
        ]);
    }
}

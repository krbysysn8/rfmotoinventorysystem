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

        $rows = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->select(
                'c.category_name',
                DB::raw('COUNT(p.product_id) as total_items'),
                DB::raw('SUM(CASE WHEN p.stock_qty = 0 THEN 1 ELSE 0 END) as out_of_stock'),
                DB::raw('SUM(CASE WHEN p.stock_qty > 0 AND p.stock_qty <= p.reorder_level THEN 1 ELSE 0 END) as low_stock'),
                DB::raw('SUM(CASE WHEN p.stock_qty > p.reorder_level THEN 1 ELSE 0 END) as in_stock'),
                DB::raw('SUM(p.stock_qty * p.unit_price) as total_value')
            )
            ->where('p.is_active', true)
            ->groupBy('c.category_name', 'c.category_id')
            ->orderBy('c.category_name')
            ->get();

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
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
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
            'status'   => 'success',
            'labels'   => $months->map(fn($m) => now()->createFromFormat('Y-m', $m)->format('M'))->toArray(),
            'stock_in' => $stockIn,
            'stock_out'=> $stockOut,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/low-stock
    //  Items at or below reorder level
    // ─────────────────────────────────────────────────────────
    public function lowStock(Request $request): JsonResponse
    {
        $items = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->select(
                'p.product_id',
                'p.sku',
                'p.barcode',
                'p.product_name',
                'p.stock_qty',
                'p.reorder_level',
                DB::raw('(p.reorder_level - p.stock_qty) as shortage'),
                'c.category_name',
                DB::raw("COALESCE(s.supplier_name, '—') as supplier_name")
            )
            ->where('p.is_active', true)
            ->where('p.stock_qty', '>', 0)
            ->whereColumn('p.stock_qty', '<=', 'p.reorder_level')
            ->orderBy('p.stock_qty')
            ->get();

        return response()->json([
            'status' => 'success',
            'items'  => $items,
            'count'  => $items->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/reports/out-of-stock
    //  Items with zero stock
    // ─────────────────────────────────────────────────────────
    public function outOfStock(Request $request): JsonResponse
    {
        $items = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->select(
                'p.product_id',
                'p.sku',
                'p.barcode',
                'p.product_name',
                'p.unit_price',
                'p.reorder_level',
                'p.updated_at',
                'c.category_name',
                DB::raw("COALESCE(s.supplier_name, '—') as supplier_name")
            )
            ->where('p.is_active', true)
            ->where('p.stock_qty', 0)
            ->orderBy('p.updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'items'  => $items,
            'count'  => $items->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
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
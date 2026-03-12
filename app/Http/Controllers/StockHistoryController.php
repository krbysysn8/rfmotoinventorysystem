<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockHistoryController extends Controller
{
    // GET /api/stock-history
    public function index(): JsonResponse
    {
        $movements = DB::table('stock_movements as sm')
            ->join('products as p', 'sm.product_id', '=', 'p.product_id')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('users as u', 'sm.performed_by', '=', 'u.user_id')
            ->select([
                'sm.movement_id',
                'sm.product_id',
                'sm.movement_type',
                'sm.quantity',
                'sm.qty_before',
                'sm.qty_after',
                'sm.reference_no',
                'sm.notes',
                'sm.movement_reason',
                'sm.performed_by',
                'sm.movement_date',
                'sm.created_at',
                'p.product_name',
                'p.sku',
                'p.unit_price',
                'c.category_name',
                'u.username',
                DB::raw("COALESCE(u.full_name, u.username) as performed_by_name"),
            ])
            ->orderByDesc('sm.movement_date')
            ->orderByDesc('sm.created_at')
            ->get();

        return response()->json([
            'status'    => 'success',
            'movements' => $movements,
        ]);
    }
}
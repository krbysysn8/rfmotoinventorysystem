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
            ->leftJoin('product_variations as pv', 'sm.variation_id', '=', 'pv.variation_id')
            ->leftJoin('users as u', 'sm.performed_by', '=', 'u.user_id')
            ->select([
                'sm.movement_id',
                'sm.product_id',
                'sm.variation_id',
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
                'pv.variation_name',
                'p.sku as product_sku',
                'pv.sku as variation_sku',
                'p.unit_price',
                'c.category_name',
                'u.username',
                DB::raw("COALESCE(u.full_name, u.username) as performed_by_name"),
            ])
            ->orderByDesc('sm.movement_date')
            ->orderByDesc('sm.created_at')
            ->get()
            ->map(fn($m) => [
                'movement_id'      => $m->movement_id,
                'product_id'       => $m->product_id,
                'variation_id'     => $m->variation_id,
                'movement_type'    => $m->movement_type,
                'quantity'         => $m->quantity,
                'qty_before'       => $m->qty_before,
                'qty_after'        => $m->qty_after,
                'reference_no'     => $m->reference_no,
                'notes'            => $m->notes,
                'movement_reason'  => $m->movement_reason,
                'performed_by'     => $m->performed_by,
                'movement_date'    => $m->movement_date,
                'created_at'       => $m->created_at,
                'product_name'     => $m->product_name . ($m->variation_name ? ' — ' . $m->variation_name : ''),
                'sku'              => $m->variation_sku ?: $m->product_sku,
                'unit_price'       => $m->unit_price,
                'category_name'    => $m->category_name,
                'username'         => $m->username,
                'performed_by_name'=> $m->performed_by_name,
            ]);

        return response()->json([
            'status'    => 'success',
            'movements' => $movements,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ReturnRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(): JsonResponse
    {
        $returns = ReturnRequest::orderByDesc('return_date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $r->logged_by_name = DB::table('users')
                    ->where('user_id', $r->logged_by)
                    ->value('full_name') ?? "User #{$r->logged_by}";
                if (!$r->product_name && $r->product_id) {
                    $r->product_name = DB::table('products')
                        ->where('product_id', $r->product_id)
                        ->value('product_name') ?? null;
                }
                return $r;
            });

        return response()->json(['status' => 'success', 'returns' => $returns]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id'     => 'nullable|string|max:100',
            'product_id'   => 'nullable|integer|exists:products,product_id',
            'variation_id' => 'nullable|integer|exists:product_variations,variation_id',
            'product_name' => 'nullable|string|max:255',
            'platform'     => 'required|in:shopee,tiktok,lazada,other',
            'courier'      => 'required|in:jnt,shopee_express,flash,other',
            'item_status'  => 'required|in:good,bad',
            'bad_reason'   => 'nullable|in:defective,damaged,no_item,wrong_item',
            'quantity'     => 'required|integer|min:1',
            'return_date'  => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        if ($request->item_status === 'bad' && empty($request->bad_reason)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bad reason is required when item status is bad.',
                'errors'  => ['bad_reason' => ['Please select a reason for bad status.']],
            ], 422);
        }

        $qty         = (int) $request->quantity;
        $isGood      = $request->item_status === 'good';
        $user        = $request->user();
        $productId   = $request->product_id   ? (int) $request->product_id   : null;
        $variationId = $request->variation_id ? (int) $request->variation_id : null;

        $productName = $request->product_name;
        if (!$productName && $productId) {
            $productName = DB::table('products')
                ->where('product_id', $productId)
                ->value('product_name');
        }
        $productLabel = $productName ?: 'No product specified';

        // 1. Create return record
        $ret = ReturnRequest::create([
            'order_id'     => $request->order_id ?: null,
            'product_id'   => $productId,
            'variation_id' => $variationId,
            'product_name' => $productName,
            'platform'     => $request->platform,
            'courier'      => $request->courier,
            'item_status'  => $request->item_status,
            'bad_reason'   => $request->item_status === 'bad' ? $request->bad_reason : null,
            'quantity'     => $qty,
            'return_date'  => $request->return_date,
            'notes'        => $request->notes ?: null,
            'logged_by'    => $user->user_id,
        ]);

        // 2. Stock adjustment — only for good items with a linked product
        if ($productId && $isGood) {
            if ($variationId) {
                // Restore stock to the specific variation
                $variation = DB::table('product_variations')
                    ->where('variation_id', $variationId)
                    ->first();
                $before = $variation ? (int) $variation->stock_qty : 0;

                DB::table('product_variations')
                    ->where('variation_id', $variationId)
                    ->increment('stock_qty', $qty);

                // Keep products.stock_qty in sync (sum of all variations)
                $newProductStock = (int) DB::table('product_variations')
                    ->where('product_id', $productId)
                    ->where('is_active', true)
                    ->sum('stock_qty');
                DB::table('products')
                    ->where('product_id', $productId)
                    ->update(['stock_qty' => $newProductStock, 'updated_at' => now()]);

                DB::table('stock_movements')->insert([
                    'product_id'    => $productId,
                    'variation_id'  => $variationId,
                    'movement_type' => 'in',
                    'quantity'      => $qty,
                    'qty_before'    => $before,
                    'qty_after'     => $before + $qty,
                    'reference_no'  => "RETURN-{$ret->id}",
                    'notes'         => "Return #{$ret->id} — item returned in good condition. Stock restored.",
                    'performed_by'  => $user->user_id,
                    'movement_date' => now()->toDateString(),
                    'created_at'    => now(),
                ]);
            } else {
                // No variation — restore directly to product
                $before = (int) DB::table('products')
                    ->where('product_id', $productId)
                    ->value('stock_qty');

                DB::table('products')
                    ->where('product_id', $productId)
                    ->increment('stock_qty', $qty);

                DB::table('stock_movements')->insert([
                    'product_id'    => $productId,
                    'variation_id'  => null,
                    'movement_type' => 'in',
                    'quantity'      => $qty,
                    'qty_before'    => $before,
                    'qty_after'     => $before + $qty,
                    'reference_no'  => "RETURN-{$ret->id}",
                    'notes'         => "Return #{$ret->id} — item returned in good condition. Stock restored.",
                    'performed_by'  => $user->user_id,
                    'movement_date' => now()->toDateString(),
                    'created_at'    => now(),
                ]);
            }
        }

        // 3. Activity log
        $platformLabel = match($request->platform) {
            'shopee' => 'Shopee', 'tiktok' => 'TikTok Shop',
            'lazada' => 'Lazada', default  => 'Other',
        };
        $courierLabel = match($request->courier) {
            'jnt'            => 'J&T Express',
            'shopee_express' => 'Shopee Express',
            'flash'          => 'Flash Express',
            default          => 'Other',
        };
        $badLabel = $request->bad_reason ? match($request->bad_reason) {
            'defective'  => 'Defective',
            'damaged'    => 'Damaged',
            'no_item'    => 'No Item',
            'wrong_item' => 'Wrong Item',
            default      => $request->bad_reason,
        } : null;

        $statusLabel = $isGood ? 'Good' : "Bad ({$badLabel})";
        $orderNote   = $request->order_id ? " Platform Order: {$request->order_id}." : '';
        $stockNote   = $productId
            ? ($isGood ? " Stock restored (+{$qty})." : ' Stock NOT restored (bad item).')
            : '';

        ActivityLog::record(
            action:      'return_logged',
            subject:     "Return #{$ret->id}",
            description: "Returned item logged — Product: {$productLabel}. Platform: {$platformLabel}. Courier: {$courierLabel}. Status: {$statusLabel}. Qty: {$qty}.{$orderNote}{$stockNote}",
            user:        $user,
        );

        return response()->json(['status' => 'success', 'return' => $ret], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $ret = ReturnRequest::findOrFail($id);

        ActivityLog::record(
            action:      'deleted',
            subject:     "Return #{$id}",
            description: 'Return record deleted — Product: ' . ($ret->product_name ?: 'No product specified')
                . ". Platform: {$ret->platform}. Courier: {$ret->courier}.",
            user:        $request->user(),
        );

        $ret->delete();

        return response()->json(['status' => 'success', 'message' => 'Return deleted.']);
    }
}

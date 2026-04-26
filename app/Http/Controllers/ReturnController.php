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

    // ─────────────────────────────────────────
    //  GET /api/returns/lookup-order?order_number=SO-0001
    //  Returns the items of a sales order so the UI can
    //  pre-populate the return form with valid products/qtys
    // ─────────────────────────────────────────
    public function lookupOrder(Request $request): JsonResponse
    {
        $orderNumber = trim($request->query('order_number', ''));

        if (!$orderNumber) {
            return response()->json(['status' => 'error', 'message' => 'No order number provided.'], 422);
        }

        $order = DB::table('sales_orders')
            ->where('order_number', $orderNumber)
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            return response()->json([
                'status'  => 'not_found',
                'message' => "No completed sales order found with number \"{$orderNumber}\".",
            ], 404);
        }

        $items = DB::table('sales_order_items as soi')
            ->join('products as p', 'soi.product_id', '=', 'p.product_id')
            ->where('soi.order_id', $order->order_id)
            ->select(
                'soi.item_id',
                'soi.product_id',
                'soi.quantity',
                'soi.unit_price',
                'soi.subtotal',
                'p.product_name',
                'p.sku as product_sku',
            )
            ->get()
            ->map(fn($i) => [
                'item_id'        => $i->item_id,
                'product_id'     => $i->product_id,
                'variation_id'   => null,
                'product_name'   => $i->product_name,
                'sku'            => $i->product_sku,
                'quantity'       => (int) $i->quantity,
                'unit_price'     => (float) $i->unit_price,
                'subtotal'       => (float) $i->subtotal,
            ]);

        return response()->json([
            'status' => 'success',
            'order'  => [
                'order_id'     => $order->order_id,
                'order_number' => $order->order_number,
                'order_date'   => $order->order_date,
                'total_amount' => (float) $order->total_amount,
                'items'        => $items,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id'     => 'required|string|max:100',
            'product_id'   => 'required|integer|exists:products,product_id',
            'variation_id' => 'nullable|integer|exists:product_variations,variation_id',
            'product_name' => 'nullable|string|max:255',
            'platform'     => 'required|in:shopee,tiktok,lazada,other',
            'courier'      => 'required|in:jnt,shopee_express,flash,other',
            'item_status'  => 'required|in:good,bad',
            'bad_reason'   => 'nullable|in:defective,damaged,no_item,wrong_item',
            'quantity'     => 'required|integer|min:1',
            'unit_price'   => 'nullable|numeric|min:0',
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
        $user        = $request->user();
        $productId   = (int) $request->product_id;
        $variationId = $request->variation_id ? (int) $request->variation_id : null;
        $orderNumber = trim($request->order_id);

        // ── Validate SO number exists and is completed ──────────────
        $salesOrder = DB::table('sales_orders')
            ->where('order_number', $orderNumber)
            ->where('status', 'completed')
            ->first();

        if (!$salesOrder) {
            return response()->json([
                'status'  => 'error',
                'message' => "Sales order \"{$orderNumber}\" not found or is not a completed order.",
                'errors'  => ['order_id' => ["Sales order \"{$orderNumber}\" not found."]],
            ], 422);
        }

        // ── Validate product/variation is in the sales order ─────────
        $orderItem = DB::table('sales_order_items')
            ->where('order_id', $salesOrder->order_id)
            ->where('product_id', $productId)
            ->first();

        if (!$orderItem) {
            $productName = DB::table('products')->where('product_id', $productId)->value('product_name') ?? "Product #{$productId}";
            return response()->json([
                'status'  => 'error',
                'message' => "\"{$productName}\" was not sold in order {$orderNumber}. Please verify the order number and product.",
                'errors'  => ['product_id' => ["This product is not part of order {$orderNumber}."]],
            ], 422);
        }

        // ── Validate return quantity doesn't exceed sold quantity ─────
        // Check how much of this item has already been returned
        $alreadyReturned = DB::table('return_requests')
            ->where('order_id', $orderNumber)
            ->where('product_id', $productId)
            ->when($variationId, fn($q) => $q->where('variation_id', $variationId))
            ->sum('quantity');

        $maxReturnable = $orderItem->quantity - $alreadyReturned;

        if ($qty > $maxReturnable) {
            return response()->json([
                'status'  => 'error',
                'message' => "Cannot return {$qty} unit(s). Original qty sold: {$orderItem->quantity}. Already returned: {$alreadyReturned}. Max returnable: {$maxReturnable}.",
                'errors'  => ['quantity' => ["Max returnable quantity is {$maxReturnable}."]],
            ], 422);
        }

        $isGood = $request->item_status === 'good';

        $isGood = $request->item_status === 'good';

        // Use actual selling price from order item if not manually supplied
        $unitPrice    = $request->unit_price !== null
            ? (float) $request->unit_price
            : (float) $orderItem->unit_price;
        $refundAmount = round($unitPrice * $qty, 2);

        $productName = $request->product_name;
        if (!$productName && $productId) {
            $productName = DB::table('products')
                ->where('product_id', $productId)
                ->value('product_name');
        }
        $productLabel = $productName ?: 'No product specified';

        // 1. Create return record
        $ret = ReturnRequest::create([
            'order_id'      => $request->order_id ?: null,
            'product_id'    => $productId,
            'variation_id'  => $variationId,
            'product_name'  => $productName,
            'platform'      => $request->platform,
            'courier'       => $request->courier,
            'item_status'   => $request->item_status,
            'bad_reason'    => $request->item_status === 'bad' ? $request->bad_reason : null,
            'quantity'      => $qty,
            'unit_price'    => $unitPrice,
            'refund_amount' => $refundAmount,
            'return_date'   => $request->return_date,
            'notes'         => $request->notes ?: null,
            'logged_by'     => $user->user_id,
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

        // 3. Deduct refund_amount from the sales order of the return date
        //    Strategy: find the most recent completed sales order on return_date
        //    that contains this product, and subtract the refund amount from its total.
        //    We do NOT delete or modify line items — we just reduce total_amount.
        //    This keeps the original sale record intact while reflecting the net revenue.
        if ($refundAmount && $refundAmount > 0) {
            // Find sales order(s) on the same date that include this product
            $targetDate = $request->return_date;

            $query = DB::table('sales_orders as so')
                ->join('sales_order_items as soi', 'so.order_id', '=', 'soi.order_id')
                ->where('so.status', 'completed')
                ->whereDate('so.order_date', $targetDate);

            if ($productId) {
                $query->where('soi.product_id', $productId);
            }

            $matchedOrder = $query
                ->select('so.order_id', 'so.total_amount', 'so.order_number')
                ->orderByDesc('so.created_at')
                ->first();

            if ($matchedOrder) {
                $newTotal = max(0, (float) $matchedOrder->total_amount - $refundAmount);
                DB::table('sales_orders')
                    ->where('order_id', $matchedOrder->order_id)
                    ->update([
                        'total_amount' => $newTotal,
                        'updated_at'   => now(),  // SalesOrder has UPDATED_AT = null but raw DB update is fine
                    ]);

                // Update the return record with the linked order
                $ret->update([
                    'notes' => trim(($ret->notes ? $ret->notes . ' ' : '') .
                        "[Deducted ₱" . number_format($refundAmount, 2) . " from {$matchedOrder->order_number}]")
                ]);
            }
        }
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
        $refundNote  = $refundAmount ? " Refund: ₱" . number_format($refundAmount, 2) . "." : '';

        ActivityLog::record(
            action:      'return_logged',
            subject:     "Return #{$ret->id}",
            description: "Returned item logged — Product: {$productLabel}. Platform: {$platformLabel}. Courier: {$courierLabel}. Status: {$statusLabel}. Qty: {$qty}.{$orderNote}{$stockNote}{$refundNote}",
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

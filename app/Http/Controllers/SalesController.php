<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = SalesOrder::with(['items.product', 'servedBy'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($o) => [
                'order_id'       => $o->order_id,
                'order_number'   => $o->order_number,
                'items'          => $o->items->map(fn($i) => [
                    'product_id'   => $i->product_id,
                    'product_name' => $i->product?->product_name,
                    'quantity'     => $i->quantity,
                    'unit_price'   => $i->unit_price,
                    'subtotal'     => $i->subtotal,
                ]),
                'subtotal'       => $o->subtotal,
                'total_amount'   => $o->total_amount,
                'served_by'      => $o->servedBy?->username,
                'order_date'     => $o->order_date->toDateString(),
                'status'         => $o->status,
            ]);

        return response()->json(['status' => 'success', 'sales' => $sales]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,product_id',
            'items.*.variation_id' => 'nullable|integer|exists:product_variations,variation_id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        $orderNumber = 'SO-' . str_pad(SalesOrder::max('order_id') + 1, 4, '0', STR_PAD_LEFT);
        $user        = $request->user();

        $result = DB::transaction(function () use ($request, $orderNumber, $user) {
            $subtotal  = 0;
            $itemNames = [];

            // First pass — validate all stock before doing anything
            foreach ($request->items as $item) {
                $product     = Product::lockForUpdate()->find($item['product_id']);
                $variationId = isset($item['variation_id']) ? (int)$item['variation_id'] : null;

                if (!$product) {
                    throw new \Exception("Product #{$item['product_id']} not found.");
                }

                // Check stock from the variation row if provided, otherwise from the product
                if ($variationId) {
                    $variation = DB::table('product_variations')
                        ->where('variation_id', $variationId)
                        ->lockForUpdate()
                        ->first();
                    if (!$variation) {
                        throw new \Exception("Variation #{$variationId} not found.");
                    }
                    if ((int)$variation->stock_qty < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->sku} ({$variation->variation_name}). Available: {$variation->stock_qty}.");
                    }
                } else {
                    if ($product->stock_qty < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->sku}. Available: {$product->stock_qty}.");
                    }
                }

                $subtotal   += $item['quantity'] * $item['unit_price'];
                $itemNames[] = $variationId
                    ? "{$product->product_name} ({$variation->variation_name}) x{$item['quantity']}"
                    : "{$product->product_name} x{$item['quantity']}";
            }

            // Create the sales order
            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'subtotal'       => $subtotal,
                'total_amount'   => $subtotal,
                'served_by'      => $user->user_id,
                'status'         => 'completed',
                'order_date'     => \Carbon\Carbon::now('Asia/Manila')->toDateString(),
            ]);

            // Second pass — create items + deduct stock + record movement
            foreach ($request->items as $item) {
                $product     = Product::find($item['product_id']);
                $variationId = isset($item['variation_id']) ? (int)$item['variation_id'] : null;
                $qty         = (int) $item['quantity'];
                $price       = (float) $item['unit_price'];

                if ($variationId) {
                    // Variation sale — deduct from product_variations.stock_qty
                    $variation = DB::table('product_variations')
                        ->where('variation_id', $variationId)->first();
                    $before = (int) $variation->stock_qty;
                    $after  = $before - $qty;

                    DB::table('product_variations')
                        ->where('variation_id', $variationId)
                        ->update(['stock_qty' => $after]);

                    // Also keep products.stock_qty in sync (sum of all variations)
                    $newProductStock = (int) DB::table('product_variations')
                        ->where('product_id', $item['product_id'])
                        ->where('is_active', true)
                        ->sum('stock_qty');
                    DB::table('products')
                        ->where('product_id', $item['product_id'])
                        ->update(['stock_qty' => $newProductStock, 'updated_at' => now()]);

                } else {
                    // No variation — deduct directly from products.stock_qty
                    $before = (int) $product->stock_qty;
                    $after  = $before - $qty;
                    $product->decrement('stock_qty', $qty);
                }

                // Save order item
                SalesOrderItem::create([
                    'order_id'     => $order->order_id,
                    'product_id'   => $item['product_id'],
                    'variation_id' => $variationId,
                    'quantity'     => $qty,
                    'unit_price'   => $price,
                    'subtotal'     => $qty * $price,
                ]);

                // Record stock movement
                DB::table('stock_movements')->insert([
                    'product_id'      => $item['product_id'],
                    'variation_id'    => $variationId,
                    'movement_type'   => 'out',
                    'movement_reason' => 'sales',
                    'quantity'        => $qty,
                    'qty_before'      => $before,
                    'qty_after'       => $after,
                    'reference_no'    => $orderNumber,
                    'notes'           => "Sale — Order {$orderNumber}",
                    'performed_by'    => $user->user_id,
                    'movement_date'   => \Carbon\Carbon::now('Asia/Manila')->toDateString(),
                    'created_at'      => now(),
                ]);
            }

            return ['order' => $order, 'itemNames' => $itemNames];
        });

        $items    = implode(', ', array_slice($result['itemNames'], 0, 3));
        $more     = count($result['itemNames']) > 3
            ? ' +' . (count($result['itemNames']) - 3) . ' more'
            : '';

        ActivityLog::record(
            action:      'stock_out',
            subject:     $orderNumber,
            description: "Sale completed. Items: {$items}{$more}. Total: ₱" . number_format($result['order']->total_amount, 2) . ".",
            user:        $user,
        );

        return response()->json([
            'status'       => 'success',
            'message'      => 'Sale completed.',
            'order_number' => $result['order']->order_number,
            'total'        => $result['order']->total_amount,
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $order = SalesOrder::findOrFail($id);

        ActivityLog::record(
            action:      'deleted',
            subject:     $order->order_number,
            description: "Sales order deleted. Total: ₱" . number_format($order->total_amount, 2) . ".",
            user:        $request->user(),
        );

        $order->delete();

        return response()->json(['status' => 'success', 'message' => 'Sales order deleted.']);
    }
}

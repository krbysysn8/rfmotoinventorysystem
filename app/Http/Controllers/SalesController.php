<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
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
                'customer_name'  => $o->customer_name,
                'items'          => $o->items->map(fn($i) => [
                    'product_id'   => $i->product_id,
                    'product_name' => $i->product?->product_name,
                    'quantity'     => $i->quantity,
                    'unit_price'   => $i->unit_price,
                    'subtotal'     => $i->subtotal,
                ]),
                'subtotal'       => $o->subtotal,
                'discount'       => $o->discount,
                'total_amount'   => $o->total_amount,
                'payment_method' => $o->payment_method,
                'served_by'      => $o->servedBy?->username,
                'order_date'     => $o->order_date->toDateString(),
                'status'         => $o->status,
            ]);

        return response()->json(['status' => 'success', 'sales' => $sales]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name'      => 'nullable|string|max:120',
            'payment_method'     => 'required|in:cash,gcash,card,bank_transfer,credit',
            'discount'           => 'nullable|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $orderNumber = 'SO-' . str_pad(SalesOrder::max('order_id') + 1, 4, '0', STR_PAD_LEFT);
        $discount    = $request->discount ?? 0;
        $user        = $request->user();

        $order = DB::transaction(function () use ($request, $orderNumber, $discount, $user) {
            $subtotal  = 0;
            $itemNames = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product->stock_qty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->sku}.");
                }
                $subtotal    += $item['quantity'] * $item['unit_price'];
                $itemNames[]  = "{$product->product_name} x{$item['quantity']}";
            }

            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'customer_name'  => $request->customer_name ?? 'Walk-in',
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total_amount'   => $subtotal - $discount,
                'payment_method' => $request->payment_method,
                'served_by'      => $user->user_id,
                'status'         => 'completed',
                'order_date'     => \Carbon\Carbon::now('Asia/Manila')->toDateString(),
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $before  = $product->stock_qty;

                SalesOrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                $product->decrement('stock_qty', $item['quantity']);

                StockMovement::create([
                    'product_id'    => $item['product_id'],
                    'movement_type' => 'out',
                    'quantity'      => $item['quantity'],
                    'qty_before'    => $before,
                    'qty_after'     => $before - $item['quantity'],
                    'reference_no'  => $orderNumber,
                    'performed_by'  => $user->user_id,
                    'movement_date' => \Carbon\Carbon::now('Asia/Manila')->toDateString(),
                ]);
            }

            return ['order' => $order, 'itemNames' => $itemNames, 'subtotal' => $subtotal];
        });

        $customer = $request->customer_name ?? 'Walk-in';
        $items    = implode(', ', array_slice($order['itemNames'], 0, 3));
        $more     = count($order['itemNames']) > 3 ? ' +' . (count($order['itemNames']) - 3) . ' more' : '';

        ActivityLog::record(
            action:      'stock_out',
            subject:     $orderNumber,
            description: "Sale completed. Customer: {$customer}. Items: {$items}{$more}. Total: ₱" . number_format($order['order']->total_amount, 2) . ".",
            user:        $user,
        );

        return response()->json([
            'status'       => 'success',
            'message'      => 'Sale completed.',
            'order_number' => $order['order']->order_number,
            'total'        => $order['order']->total_amount,
        ], 201);
    }
}
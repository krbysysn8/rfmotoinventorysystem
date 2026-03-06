<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\VerifyAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('category'))
            $query->whereHas('category', fn($q) => $q->where('category_name', $request->category));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('product_name', 'like', "%$s%")
                ->orWhere('sku', 'like', "%$s%")
                ->orWhere('barcode', 'like', "%$s%")
            );
        }

        $products = $query->orderBy('product_name')->get()->map(fn($p) => [
            'id'           => $p->product_id,
            'sku'          => $p->sku,
            'barcode'      => $p->barcode,
            'name'         => $p->product_name,
            'category'     => $p->category?->category_name,
            'brand'        => $p->brand,
            'price'        => $p->unit_price,
            'cost'         => $p->cost_price,
            'stock'        => $p->stock_qty,
            'reorder'      => $p->reorder_level,
            'stock_status' => $p->stock_status,
        ]);

        return response()->json(['status' => 'success', 'products' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sku'          => 'required|unique:products,sku',
            'product_name' => 'required|string|max:200',
            'category_id'  => 'required|exists:categories,category_id',
            'brand'        => 'nullable|string|max:80',
            'unit_price'   => 'required|numeric|min:0',
            'cost_price'   => 'required|numeric|min:0',
            'stock_qty'    => 'required|integer|min:0',
            'reorder_level'=> 'required|integer|min:0',
        ]);

        $data['created_by'] = $request->user()->user_id;
        $data['barcode']    = $data['sku'] . '-A';

        $product = Product::create($data);

        return response()->json(['status' => 'success', 'product' => $product], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'product_name' => 'sometimes|string|max:200',
            'brand'        => 'nullable|string|max:80',
            'unit_price'   => 'sometimes|numeric|min:0',
            'cost_price'   => 'sometimes|numeric|min:0',
            'reorder_level'=> 'sometimes|integer|min:0',
        ]);

        $product->update($data);

        return response()->json(['status' => 'success', 'product' => $product]);
    }

    public function stockIn(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity'  => 'required|integer|min:1',
            'reference' => 'nullable|string|max:60',
            'notes'     => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);

        DB::transaction(function () use ($request, $product) {
            $before = $product->stock_qty;
            $product->increment('stock_qty', $request->quantity);

            StockMovement::create([
                'product_id'    => $product->product_id,
                'movement_type' => 'in',
                'quantity'      => $request->quantity,
                'qty_before'    => $before,
                'qty_after'     => $before + $request->quantity,
                'reference_no'  => $request->reference,
                'notes'         => $request->notes,
                'performed_by'  => $request->user()->user_id,
                'movement_date' => today(),
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => "Added {$request->quantity} units to {$product->sku}.",
            'stock'   => $product->fresh()->stock_qty,
        ]);
    }

    public function stockOut(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity'  => 'required|integer|min:1',
            'reference' => 'nullable|string|max:60',
            'notes'     => 'nullable|string',
        ]);

        $product  = Product::findOrFail($id);
        $user     = $request->user();
        $qty      = $request->quantity;

        if ($user->role->role_name === 'staff' && $qty >= 4) {
            VerifyAction::create([
                'request_no'   => 'VA-' . str_pad(VerifyAction::max('verify_id') + 1, 3, '0', STR_PAD_LEFT),
                'action_type'  => 'Large Stock Out',
                'product_id'   => $product->product_id,
                'details'      => "Remove {$qty} units (Ref: " . ($request->reference ?? 'MANUAL') . ")",
                'requested_by' => $user->user_id,
                'status'       => 'pending',
                'request_date' => today(),
            ]);

            return response()->json([
                'status'  => 'needs_verify',
                'message' => 'Request sent for admin approval.',
            ]);
        }

        if ($product->stock_qty < $qty) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient stock.',
            ], 422);
        }

        DB::transaction(function () use ($request, $product, $qty, $user) {
            $before = $product->stock_qty;
            $product->decrement('stock_qty', $qty);

            StockMovement::create([
                'product_id'    => $product->product_id,
                'movement_type' => 'out',
                'quantity'      => $qty,
                'qty_before'    => $before,
                'qty_after'     => $before - $qty,
                'reference_no'  => $request->reference,
                'notes'         => $request->notes,
                'performed_by'  => $user->user_id,
                'movement_date' => today(),
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => "Removed {$qty} units from {$product->sku}.",
            'stock'   => $product->fresh()->stock_qty,
        ]);
    }
}
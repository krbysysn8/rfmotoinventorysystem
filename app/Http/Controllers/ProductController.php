<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ─────────────────────────────────────────────────────────
    //  GET /api/products  — list all products with variations
    // ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search   = $request->query('search');

        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->select(
                'p.id',
                'p.sku',
                'p.barcode',
                'p.name',
                'p.description',
                'p.brand',
                'p.price',
                'p.cost',
                'p.stock_qty as stock',
                'p.reorder_level as reorder',
                'p.is_active',
                'c.name as category',
                'c.icon as category_icon',
                'c.gradient as category_gradient'
            )
            ->where('p.is_active', true);

        if ($category && $category !== 'All') {
            $query->where('c.name', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereILike('p.name', "%{$search}%")
                  ->orWhereILike('p.sku', "%{$search}%")
                  ->orWhereILike('p.brand', "%{$search}%");
            });
        }

        $products = $query->orderBy('c.name')->orderBy('p.name')->get();

        // Attach variations for each product
        $productIds = $products->pluck('id');

        $variations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('product_id');

        $products = $products->map(function ($product) use ($variations) {
            $product->variations = $variations->get($product->id, collect())->values();
            return $product;
        });

        return response()->json([
            'status'   => 'success',
            'products' => $products,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/products/{id}  — single product with variations
    // ─────────────────────────────────────────────────────────
    public function show(int $id)
    {
        $product = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->select(
                'p.id', 'p.sku', 'p.barcode', 'p.name', 'p.description',
                'p.brand', 'p.price', 'p.cost',
                'p.stock_qty as stock', 'p.reorder_level as reorder', 'p.is_active',
                'c.name as category', 'c.icon as category_icon', 'c.gradient as category_gradient'
            )
            ->where('p.id', $id)
            ->where('p.is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $product->variations = DB::table('product_variations')
            ->where('product_id', $id)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['status' => 'success', 'product' => $product]);
    }

    // ─────────────────────────────────────────────────────────
    //  POST /api/products  — create product (admin only)
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku'           => 'required|string|max:50|unique:products,sku',
            'barcode'       => 'required|string|max:100|unique:products,barcode',
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string',
            'category_id'   => 'required|integer|exists:categories,id',
            'brand'         => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'cost'          => 'required|numeric|min:0',
            'stock_qty'     => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'variations'    => 'nullable|array',
            'variations.*.label' => 'required|string|max:100',
            'variations.*.color' => 'required|string|max:30',
            'variations.*.stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $productId = DB::table('products')->insertGetId([
                'sku'           => $request->sku,
                'barcode'       => $request->barcode,
                'name'          => $request->name,
                'description'   => $request->description,
                'category_id'   => $request->category_id,
                'brand'         => $request->brand,
                'price'         => $request->price,
                'cost'          => $request->cost,
                'stock_qty'     => $request->stock_qty,
                'reorder_level' => $request->reorder_level,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Insert variations if provided
            if ($request->has('variations') && is_array($request->variations)) {
                foreach ($request->variations as $index => $var) {
                    DB::table('product_variations')->insert([
                        'product_id'  => $productId,
                        'label'       => $var['label'],
                        'color'       => $var['color'] ?? '#17b8dc',
                        'stock'       => $var['stock'] ?? 0,
                        'sort_order'  => $index,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            } else {
                // Default single variation
                DB::table('product_variations')->insert([
                    'product_id'  => $productId,
                    'label'       => 'Standard',
                    'color'       => '#17b8dc',
                    'stock'       => $request->stock_qty,
                    'sort_order'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'Product created successfully.',
                'product_id' => $productId,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    //  PUT /api/products/{id}  — update product (admin only)
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'sku'           => "required|string|max:50|unique:products,sku,{$id}",
            'barcode'       => "required|string|max:100|unique:products,barcode,{$id}",
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string',
            'category_id'   => 'required|integer|exists:categories,id',
            'brand'         => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'cost'          => 'required|numeric|min:0',
            'stock_qty'     => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::table('products')->where('id', $id)->update([
            'sku'           => $request->sku,
            'barcode'       => $request->barcode,
            'name'          => $request->name,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'brand'         => $request->brand,
            'price'         => $request->price,
            'cost'          => $request->cost,
            'stock_qty'     => $request->stock_qty,
            'reorder_level' => $request->reorder_level,
            'updated_at'    => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Product updated successfully.']);
    }

    // ─────────────────────────────────────────────────────────
    //  DELETE /api/products/{id}  — soft-delete (admin only)
    // ─────────────────────────────────────────────────────────
    public function destroy(int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        DB::table('products')->where('id', $id)->update([
            'is_active'  => false,
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Product deactivated.']);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /api/categories  — for dropdowns / filter chips
    // ─────────────────────────────────────────────────────────
    public function categories()
    {
        $categories = DB::table('categories')
            ->select('id', 'name', 'icon', 'gradient')
            ->orderBy('name')
            ->get();

        return response()->json(['status' => 'success', 'categories' => $categories]);
    }

    // ─────────────────────────────────────────────────────────
    //  PUT /api/products/{id}/variations  — update variations
    // ─────────────────────────────────────────────────────────
    public function updateVariations(Request $request, int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'variations'         => 'required|array|min:1',
            'variations.*.label' => 'required|string|max:100',
            'variations.*.color' => 'required|string|max:30',
            'variations.*.stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Delete existing variations
            DB::table('product_variations')->where('product_id', $id)->delete();

            // Re-insert
            foreach ($request->variations as $index => $var) {
                DB::table('product_variations')->insert([
                    'product_id'  => $id,
                    'label'       => $var['label'],
                    'color'       => $var['color'],
                    'stock'       => $var['stock'],
                    'sort_order'  => $index,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Variations updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
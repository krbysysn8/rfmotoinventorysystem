<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ── GET /inventory  — blade view ─────────────────────────
    public function inventoryView()
    {
        return view('inventory');
    }

    // Helper: check if subcategory_id column exists (cached)
    private static ?bool $hasSubcat = null;
    private static function hasSubcategoryColumn(): bool
    {
        if (self::$hasSubcat === null) {
            self::$hasSubcat = Schema::hasColumn('products', 'subcategory_id');
        }
        return self::$hasSubcat;
    }

    // ── GET /api/products ─────────────────────────────────────
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search   = $request->query('search');
        $hasSubcat = self::hasSubcategoryColumn();

        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id');

        $selectCols = [
            'p.product_id', 'p.sku', 'p.barcode', 'p.product_name', 'p.description',
            'p.brand', 'p.unit_price',
            'p.stock_qty as stock', 'p.reorder_level as reorder',
            'p.is_active', 'p.image_url', 'p.updated_at',
            'p.category_id', 'p.supplier_id',
            'c.category_name as category',
            's.supplier_name',
        ];

        if (Schema::hasColumn('products', 'color')) {
            $selectCols[] = 'p.color';
        }

        if ($hasSubcat) {
            $query->leftJoin('subcategories as sc', 'p.subcategory_id', '=', 'sc.subcategory_id');
            $selectCols[] = 'p.subcategory_id';
            $selectCols[] = 'sc.subcategory_name';
        }

        $query->select($selectCols)->where('p.is_active', true);

        if ($category && $category !== 'All') {
            $query->where('c.category_name', $category);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(p.product_name) LIKE ?', [strtolower("%{$search}%")])
                  ->orWhereRaw('LOWER(p.sku) LIKE ?',        [strtolower("%{$search}%")])
                  ->orWhereRaw('LOWER(p.brand) LIKE ?',      [strtolower("%{$search}%")]);
            });
        }

        $products   = $query->orderBy('c.category_name')->orderBy('p.product_name')->get();
        $productIds = $products->pluck('product_id');

        try {
            $variations = DB::table('product_variations')
                ->whereIn('product_id', $productIds)
                ->where('is_active', true)
                ->orderBy('product_id')->orderBy('sort_order')
                ->get()->groupBy('product_id');
        } catch (\Throwable $e) {
            $variations = collect();
        }

        $products = $products->map(function ($product) use ($variations) {
            $vars = $variations->get($product->product_id, collect())->values();
            $product->variations = $vars;
            // Effective stock = sum of all active variation stocks (if any), else product stock_qty
            $baseStock = isset($product->stock) ? (int)$product->stock : (int)($product->stock_qty ?? 0);
            $product->effective_stock = $vars->isNotEmpty()
                ? (int)$vars->sum('stock_qty')
                : $baseStock;
            return $product;
        });

        return response()->json(['status' => 'success', 'products' => $products]);
    }

    // ── GET /api/products/{id} ────────────────────────────────
    public function show(int $id)
    {
        $hasSubcat = self::hasSubcategoryColumn();

        $query = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.supplier_id');

        $selectCols = [
            'p.product_id', 'p.sku', 'p.barcode', 'p.product_name', 'p.description',
            'p.brand', 'p.unit_price',
            'p.stock_qty as stock', 'p.reorder_level as reorder',
            'p.is_active', 'p.image_url', 'p.updated_at',
            'p.category_id', 'p.supplier_id',
            'c.category_name as category',
            's.supplier_name',
        ];

        if (Schema::hasColumn('products', 'color')) {
            $selectCols[] = 'p.color';
        }

        if ($hasSubcat) {
            $query->leftJoin('subcategories as sc', 'p.subcategory_id', '=', 'sc.subcategory_id');
            $selectCols[] = 'p.subcategory_id';
            $selectCols[] = 'sc.subcategory_name';
        }

        $product = $query->select($selectCols)
            ->where('p.product_id', $id)
            ->where('p.is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $vars = DB::table('product_variations')
            ->where('product_id', $id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $product->variations      = $vars;
        $baseStock = isset($product->stock) ? (int)$product->stock : (int)($product->stock_qty ?? 0);
        $product->effective_stock = $vars->isNotEmpty()
            ? (int)$vars->sum('stock_qty')
            : $baseStock;

        return response()->json(['status' => 'success', 'product' => $product]);
    }

    // cached column checks
    private static ?bool $hasProductColor    = null;
    private static ?bool $hasVariationColor  = null;
    private static function hasProductColor(): bool {
        if (self::$hasProductColor === null)
            self::$hasProductColor = Schema::hasColumn('products', 'color');
        return self::$hasProductColor;
    }
    private static function hasVariationColor(): bool {
        if (self::$hasVariationColor === null)
            self::$hasVariationColor = Schema::hasColumn('product_variations', 'color');
        return self::$hasVariationColor;
    }

    // ── POST /api/products ────────────────────────────────────
    public function store(Request $request)
    {
        $variationsRaw = $request->input('variations');
        if (is_string($variationsRaw)) {
            $decoded = json_decode($variationsRaw, true);
            if (is_array($decoded)) {
                $request->merge(['variations' => $decoded]);
            }
        }

        $validator = Validator::make($request->all(), [
            'sku'                         => 'required|string|max:50|unique:products,sku',
            'barcode'                      => 'nullable|string|max:100|unique:products,barcode',
            'product_name'                => 'required|string|max:200',
            'description'                 => 'nullable|string',
            'category_id'                 => 'required|integer|exists:categories,category_id',
            'brand'                       => 'nullable|string|max:100',
            'unit_price'                  => 'required|numeric|min:0',
            'stock_qty'                   => 'required|integer|min:0',
            'reorder_level'               => 'required|integer|min:0',
            'photo'                       => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'variation_photo_*'           => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'variations'                  => 'nullable|array',
            'variations.*.variation_name' => 'required_with:variations|string|max:100',
            'variations.*.unit_price'     => 'nullable|numeric|min:0',
            'variations.*.stock_qty'      => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $imageUrl = null;
        if ($request->hasFile('photo')) {
            $path     = $request->file('photo')->store('products', 'public');
            $imageUrl = Storage::url($path);
        }

        DB::beginTransaction();
        try {
            $insertData = [
                'sku'           => $request->sku,
                'barcode'       => $request->barcode ?: $request->sku,
                'product_name'  => $request->product_name,
                'description'   => $request->description,
                'category_id'   => $request->category_id,
                'supplier_id'   => $request->supplier_id    ?: null,
                'brand'         => $request->brand,
                'unit_price'    => $request->unit_price,
                'stock_qty'     => $request->stock_qty,
                'reorder_level' => $request->reorder_level,
                'image_url'     => $imageUrl,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (Schema::hasColumn('products', 'color')) {
                $insertData['color'] = $request->color ?: '#17b8dc';
            }
            if (self::hasSubcategoryColumn()) {
                $insertData['subcategory_id'] = $request->subcategory_id ?: null;
            }

            $productId = DB::table('products')->insertGetId($insertData, 'product_id');

            if ($request->has('variations') && is_array($request->variations)) {
                foreach ($request->variations as $index => $var) {
                    $varSku      = $var['sku'] ?? null;
                    $varBarcode  = $var['barcode'] ?? $varSku ?? ($request->sku . '-' . ($index + 1));

                    // Handle per-variation image upload
                    $varImageUrl = null;
                    $varPhotoKey = 'variation_photo_' . $index;
                    if ($request->hasFile($varPhotoKey)) {
                        $varPath     = $request->file($varPhotoKey)->store('products/variations', 'public');
                        $varImageUrl = Storage::url($varPath);
                    }

                    $varInsert = [
                        'product_id'     => $productId,
                        'variation_name' => $var['variation_name'],
                        'sku'            => $varSku,
                        'barcode'        => $varBarcode,
                        'unit_price'     => $var['unit_price']  ?? $request->unit_price,
                        'stock_qty'      => $var['stock_qty']   ?? 0,
                        'image_url'      => $varImageUrl,
                        'sort_order'     => $index,
                        'is_active'      => true,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    if (self::hasVariationColor()) {
                        $varInsert['color'] = $var['color'] ?? '#17b8dc';
                    }
                    DB::table('product_variations')->insert($varInsert);
                }
            }

            // Sync products.stock_qty = sum of all variation stocks
            if ($request->has('variations') && is_array($request->variations)) {
                $varTotal = array_sum(array_column($request->variations, 'stock_qty'));
                DB::table('products')->where('product_id', $productId)->update(['stock_qty' => (int)$varTotal]);
            }

            DB::commit();
            Cache::forget('active_products');

            ActivityLog::record(
                action:      'stock_in',
                subject:     $request->product_name,
                description: "New product added. SKU: {$request->sku}. Initial stock: {$request->stock_qty} units.",
                user:        $request->user(),
            );

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

    // ── PUT /api/products/{id} ────────────────────────────────
    public function update(Request $request, int $id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $variationsRaw = $request->input('variations');
        if (is_string($variationsRaw)) {
            $decoded = json_decode($variationsRaw, true);
            if (is_array($decoded)) {
                $request->merge(['variations' => $decoded]);
            }
        }

        $validator = Validator::make($request->all(), [
            'sku'           => "required|string|max:50|unique:products,sku,{$id},product_id",
            'barcode'       => "nullable|string|max:100|unique:products,barcode,{$id},product_id",
            'product_name'  => 'required|string|max:200',
            'description'   => 'nullable|string',
            'category_id'   => 'required|integer|exists:categories,category_id',
            'brand'         => 'nullable|string|max:100',
            'unit_price'    => 'required|numeric|min:0',
            'stock_qty'     => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'photo'         => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'variation_photo_*' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $updateData = [
            'sku'           => $request->sku,
            'barcode'       => $request->barcode ?: $request->sku,
            'product_name'  => $request->product_name,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'supplier_id'   => $request->supplier_id ?: null,
            'brand'         => $request->brand,
            'unit_price'    => $request->unit_price,
            'stock_qty'     => $request->stock_qty,
            'reorder_level' => $request->reorder_level,
            'updated_at'    => now(),
        ];

        if (Schema::hasColumn('products', 'color')) {
            $updateData['color'] = $request->color ?: ($product->color ?? '#17b8dc');
        }

        if (self::hasSubcategoryColumn()) {
            $updateData['subcategory_id'] = $request->subcategory_id ?: null;
        }

        if ($request->hasFile('photo')) {
            if ($product->image_url) {
                $oldPath = str_replace('/storage/', 'public/', $product->image_url);
                Storage::delete($oldPath);
            }
            $path = $request->file('photo')->store('products', 'public');
            $updateData['image_url'] = Storage::url($path);
        }

        $stockChange = (int)$request->stock_qty - (int)$product->stock_qty;
        $stockNote   = $stockChange > 0
            ? " Stock adjusted: +{$stockChange} units."
            : ($stockChange < 0 ? " Stock adjusted: {$stockChange} units." : '');

        DB::beginTransaction();
        try {
            DB::table('products')->where('product_id', $id)->update($updateData);

            if ($request->has('variations') && is_array($request->variations)) {
                DB::table('product_variations')->where('product_id', $id)->delete();
                foreach ($request->variations as $index => $var) {
                    $varSku      = $var['sku'] ?? null;
                    $varBarcode  = $var['barcode'] ?? $varSku ?? ($request->sku . '-' . ($index + 1));

                    // Handle per-variation image upload
                    $varImageUrl = $var['image_url'] ?? null;
                    $varPhotoKey = 'variation_photo_' . $index;
                    if ($request->hasFile($varPhotoKey)) {
                        $varPath     = $request->file($varPhotoKey)->store('products/variations', 'public');
                        $varImageUrl = Storage::url($varPath);
                    }

                    $varInsert = [
                        'product_id'     => $id,
                        'variation_name' => $var['variation_name'],
                        'sku'            => $varSku,
                        'barcode'        => $varBarcode,
                        'unit_price'     => $var['unit_price'] ?? $request->unit_price,
                        'stock_qty'      => $var['stock_qty']  ?? 0,
                        'image_url'      => $varImageUrl,
                        'sort_order'     => $index,
                        'is_active'      => true,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    if (self::hasVariationColor()) {
                        $varInsert['color'] = $var['color'] ?? '#17b8dc';
                    }
                    DB::table('product_variations')->insert($varInsert);
                }
            }

            // Sync products.stock_qty = sum of all active variation stocks
            if ($request->has('variations') && is_array($request->variations)) {
                $varTotal = array_sum(array_column($request->variations, 'stock_qty'));
                DB::table('products')->where('product_id', $id)->update(['stock_qty' => (int)$varTotal, 'updated_at' => now()]);
            }

            DB::commit();
            Cache::forget('active_products');

            ActivityLog::record(
                action:      'item_updated',
                subject:     $request->product_name,
                description: "Product details updated. SKU: {$request->sku}.{$stockNote}",
                user:        $request->user(),
            );

            return response()->json(['status' => 'success', 'message' => 'Product updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ── DELETE /api/products/{id} — soft delete ───────────────
    public function destroy(Request $request, int $id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        DB::table('products')->where('product_id', $id)->update([
            'is_active'  => false,
            'updated_at' => now(),
        ]);

        Cache::forget('active_products');

        ActivityLog::record(
            action:      'deleted',
            subject:     $product->product_name,
            description: "Product deactivated. SKU: {$product->sku}.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Product deactivated.']);
    }

    // ── GET /api/product-categories — for dropdowns ──────────
    public function categories()
    {
        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->orderBy('category_name')
            ->get();

        return response()->json(['status' => 'success', 'categories' => $categories]);
    }

    // ── PUT /api/products/{id}/variations ─────────────────────
    public function updateVariations(Request $request, int $id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'variations'                  => 'required|array|min:1',
            'variations.*.variation_name' => 'required|string|max:100',
            'variations.*.unit_price'     => 'nullable|numeric|min:0',
            'variations.*.stock_qty'      => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('product_variations')->where('product_id', $id)->delete();

            $parentSku = DB::table('products')->where('product_id', $id)->value('sku');
            foreach ($request->variations as $index => $var) {
                $varSku     = $var['sku'] ?? null;
                $varBarcode = $var['barcode'] ?? $varSku ?? ($parentSku . '-' . ($index + 1));
                $varInsert = [
                    'product_id'     => $id,
                    'variation_name' => $var['variation_name'],
                    'sku'            => $varSku,
                    'barcode'        => $varBarcode,
                    'unit_price'     => $var['unit_price'] ?? null,
                    'stock_qty'      => $var['stock_qty']  ?? 0,
                    'sort_order'     => $index,
                    'is_active'      => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
                if (self::hasVariationColor()) {
                    $varInsert['color'] = $var['color'] ?? '#17b8dc';
                }
                DB::table('product_variations')->insert($varInsert);
            }

            DB::commit();

            ActivityLog::record(
                action:      'item_updated',
                subject:     $product->product_name,
                description: "Product variations updated. " . count($request->variations) . " variation(s) saved.",
                user:        $request->user(),
            );

            return response()->json(['status' => 'success', 'message' => 'Variations updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

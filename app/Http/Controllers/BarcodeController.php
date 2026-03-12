<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{

    private $categoryCodeMap = [
        'Engine Parts'   => '01',
        'Electrical'     => '02',
        'Brake System'   => '03',
        'Suspension'     => '04',
        'Body & Frame'   => '05',
        'Transmission'   => '06',
        'Cooling System' => '07',
        'Exhaust'        => '08',
        'Filters'        => '09',
        'Oils & Fluids'  => '10',
    ];

    private function computeEAN13(int $productId, string $categoryName): string
    {
        $catCode = $this->categoryCodeMap[$categoryName] ?? '00';
        $payload = '200' . $catCode . str_pad($productId, 7, '0', STR_PAD_LEFT);

        // EAN-13 check digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$payload[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $check = (10 - ($sum % 10)) % 10;

        return $payload . $check;
    }

    public function lookup(Request $request)
    {
        $code = trim($request->query('code', ''));

        if (!$code) {
            return response()->json(['status' => 'error', 'message' => 'No barcode provided.'], 422);
        }

        // 1. Match products.barcode or products.sku
        $product = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->select('p.product_id','p.sku','p.barcode','p.product_name','p.brand',
                     'p.unit_price','p.cost_price','p.stock_qty','p.reorder_level',
                     'p.is_active','c.category_name')
            ->where('p.is_active', true)
            ->where(function ($q) use ($code) {
                $q->where('p.barcode', $code)->orWhere('p.sku', $code);
            })
            ->first();

        // 2. Match product_variations.barcode or variation.sku
        if (!$product) {
            $varRow = DB::table('product_variations as pv')
                ->join('products as p', 'pv.product_id', '=', 'p.product_id')
                ->join('categories as c', 'p.category_id', '=', 'c.category_id')
                ->where('p.is_active', true)
                ->where(function ($q) use ($code) {
                    $q->where('pv.barcode', $code)->orWhere('pv.sku', $code);
                })
                ->select('p.product_id','p.sku','p.barcode','p.product_name','p.brand',
                         'p.unit_price','p.cost_price','p.reorder_level','p.is_active',
                         'c.category_name',
                         'pv.stock_qty','pv.variation_name',
                         DB::raw('pv.barcode as variation_barcode'),
                         DB::raw('pv.sku as variation_sku'))
                ->first();
            if ($varRow) $product = $varRow;
        }

        // 3. Match computed EAN-13
        if (!$product) {
            $all = DB::table('products as p')
                ->join('categories as c', 'p.category_id', '=', 'c.category_id')
                ->select('p.product_id','p.sku','p.barcode','p.product_name','p.brand',
                         'p.unit_price','p.cost_price','p.stock_qty','p.reorder_level',
                         'p.is_active','c.category_name')
                ->where('p.is_active', true)->get();
            foreach ($all as $p) {
                if ($this->computeEAN13($p->product_id, $p->category_name) === $code) {
                    $product = $p; break;
                }
            }
        }

        if (!$product) {
            return response()->json([
                'status'  => 'not_found',
                'code'    => $code,
                'message' => 'No product found for this barcode.',
            ]);
        }

        // Auto-assign barcode if missing (realtime fix for existing products)
        if (empty($product->barcode)) {
            $autoCode = $product->sku;
            DB::table('products')->where('product_id', $product->product_id)
                ->update(['barcode' => $autoCode]);
            $product->barcode = $autoCode;
        }

        // Use stored barcode as the scan code (not recomputed EAN-13)
        $scanCode = $product->barcode ?: $product->sku;

        // Include variation info if matched via variation
        $variationName = $product->variation_name ?? null;
        $variationCode = $product->variation_barcode ?? null;
        $variationSku  = $product->variation_sku ?? null;

        return response()->json([
            'status'  => 'found',
            'product' => [
                'product_id'     => $product->product_id,
                'sku'            => $variationSku ?: $product->sku,
                'barcode'        => $variationCode ?: $scanCode,
                'ean13'          => $variationCode ?: $scanCode,
                'product_name'   => $product->product_name . ($variationName ? ' — ' . $variationName : ''),
                'brand'          => $product->brand,
                'unit_price'     => (float) $product->unit_price,
                'cost_price'     => (float) ($product->cost_price ?? 0),
                'stock_qty'      => (int) $product->stock_qty,
                'reorder_level'  => (int) $product->reorder_level,
                'category_name'  => $product->category_name,
                'variation_name' => $variationName,
            ],
        ]);
    }

    // ─────────────────────────────────────────
    //  POST /api/barcode/generate
    //  Generate & assign EAN-13 to a product
    // ─────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,product_id']);

        $product = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->select('p.product_id', 'p.sku', 'p.product_name', 'p.barcode', 'c.category_name')
            ->where('p.product_id', $request->product_id)
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $ean13 = $this->computeEAN13($product->product_id, $product->category_name);

        // Save to products.barcode column
        DB::table('products')
            ->where('product_id', $product->product_id)
            ->update(['barcode' => $ean13]);

        // Also upsert into barcodes table
        $existing = DB::table('barcodes')
            ->where('product_id', $product->product_id)
            ->where('barcode_type', 'EAN13')
            ->first();

        if ($existing) {
            DB::table('barcodes')
                ->where('barcode_id', $existing->barcode_id)
                ->update([
                    'barcode_value' => $ean13,
                    'assigned_by'   => Auth::id(),
                    'assigned_at'   => now(),
                ]);
        } else {
            DB::table('barcodes')->insert([
                'product_id'    => $product->product_id,
                'barcode_value' => $ean13,
                'barcode_type'  => 'EAN13',
                'is_primary'    => true,
                'assigned_by'   => Auth::id(),
                'assigned_at'   => now(),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'ean13'   => $ean13,
            'product' => $product->product_name,
            'sku'     => $product->sku,
        ]);
    }

    public function stockUpdate(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|integer|exists:products,product_id',
            'action'        => 'required|in:add-existing,stock-out',
            'quantity'      => 'required|integer|min:1',
            'reference_no'  => 'nullable|string|max:60',
            'notes'         => 'nullable|string|max:255',
        ]);

        $product = DB::table('products')
            ->where('product_id', $request->product_id)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $qty    = (int) $request->quantity;
        $action = $request->action;
        $before = (int) $product->stock_qty;

        if ($action === 'stock-out') {
            if ($qty > $before) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Not enough stock. Current: {$before}, Requested: {$qty}.",
                ], 422);
            }

            if ($qty >= 4 && Auth::user()?->role !== 'admin') {
                return response()->json([
                    'status'  => 'requires_verify',
                    'message' => "Large stock-out of {$qty} units requires admin verification.",
                    'qty'     => $qty,
                ], 200);
            }
        }

        DB::beginTransaction();
        try {
            $after = $action === 'add-existing' ? $before + $qty : $before - $qty;

            DB::table('products')
                ->where('product_id', $product->product_id)
                ->update(['stock_qty' => $after, 'updated_at' => now()]);

            DB::table('stock_movements')->insert([
                'product_id'    => $product->product_id,
                'movement_type' => $action === 'add-existing' ? 'in' : 'out',
                'quantity'      => $qty,
                'qty_before'    => $before,
                'qty_after'     => $after,
                'reference_no'  => $request->reference_no,
                'notes'         => $request->notes ?? ($action === 'add-existing' ? 'Stock In via Barcode Scan' : 'Stock Out via Barcode Scan'),
                'performed_by'  => Auth::id(),
                'movement_date' => now()->toDateString(),
                'created_at'    => now(),
            ]);

            // Log scan
            DB::table('scan_logs')->insert([
                'product_id'  => $product->product_id,
                'scanned_code'=> $request->scanned_code ?? $product->barcode ?? $product->sku,
                'action'      => $action,
                'quantity'    => $qty,
                'performed_by'=> Auth::id(),
                'scanned_at'  => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => $action === 'add-existing' ? "Added {$qty} units." : "Removed {$qty} units.",
                'qty_before' => $before,
                'qty_after'  => $after,
                'product_id' => $product->product_id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function products(Request $request)
    {
        $rows = DB::table('products as p')
            ->join('categories as c', 'p.category_id', '=', 'c.category_id')
            ->select('p.product_id','p.sku','p.barcode','p.product_name',
                     'p.brand','p.unit_price','p.stock_qty','p.reorder_level',
                     'p.image_url','c.category_name')
            ->where('p.is_active', true)
            ->orderBy('c.category_name')->orderBy('p.sku')->get();

        $productIds = $rows->pluck('product_id');

        // Auto-assign missing product barcodes (SKU fallback) — realtime fix
        foreach ($rows as $p) {
            if (!$p->barcode) {
                DB::table('products')->where('product_id', $p->product_id)
                    ->update(['barcode' => $p->sku]);
                $p->barcode = $p->sku;
            }
        }

        $allVariations = DB::table('product_variations')
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->orderBy('product_id')->orderBy('sort_order')
            ->get()->groupBy('product_id');

        // Auto-assign missing variation barcodes — realtime fix
        foreach ($allVariations->flatten() as $v) {
            if (!$v->barcode) {
                $parentSku = $rows->firstWhere('product_id', $v->product_id)->sku ?? 'PROD';
                $autoCode  = $v->sku ?: ($parentSku . '-' . $v->variation_id);
                DB::table('product_variations')->where('variation_id', $v->variation_id)
                    ->update(['barcode' => $autoCode]);
                $v->barcode = $autoCode;
            }
        }

        $products = $rows->map(function ($p) use ($allVariations) {
            $vars = $allVariations->get($p->product_id, collect());
            return [
                'product_id'    => $p->product_id,
                'sku'           => $p->sku,
                'barcode'       => $p->barcode,
                'ean13'         => $p->barcode, // use stored barcode as scan code
                'product_name'  => $p->product_name,
                'brand'         => $p->brand,
                'unit_price'    => (float) $p->unit_price,
                'stock_qty'     => (int) $p->stock_qty,
                'reorder_level' => (int) $p->reorder_level,
                'image_url'     => $p->image_url,
                'category_name' => $p->category_name,
                'variations'    => $vars->map(function ($v) use ($p) {
                    return [
                        'variation_id'   => $v->variation_id,
                        'variation_name' => $v->variation_name,
                        'sku'            => $v->sku ?? $p->sku,
                        'barcode'        => $v->barcode,
                        'image_url'      => $v->image_url ?? null,
                        'stock_qty'      => (int) $v->stock_qty,
                    ];
                })->values(),
            ];
        });

        return response()->json(['status' => 'success', 'products' => $products]);
    }

    public function scanLogs(Request $request)
    {
        $logs = DB::table('scan_logs as sl')
            ->leftJoin('products as p', 'sl.product_id', '=', 'p.product_id')
            ->leftJoin('users as u', 'sl.performed_by', '=', 'u.user_id')
            ->select(
                'sl.log_id',
                'sl.scanned_code',
                'sl.action',
                'sl.quantity',
                'sl.scanned_at',
                'p.product_name',
                'p.sku',
                'p.stock_qty',
                'u.username'
            )
            ->orderByDesc('sl.scanned_at')
            ->limit(50)
            ->get();

        return response()->json(['status' => 'success', 'logs' => $logs]);
    }
    public function clearScanLogs(Request $request)
    {
        DB::table('scan_logs')
            ->where('performed_by', Auth::id())
            ->delete();
        return response()->json(['status' => 'success', 'message' => 'Scan logs cleared.']);
    }

    public function index()
    {
        return view('barcode');
    }
}
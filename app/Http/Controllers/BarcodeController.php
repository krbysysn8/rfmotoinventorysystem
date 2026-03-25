<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

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
                     'p.unit_price','p.stock_qty','p.reorder_level',
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
                         'p.unit_price','p.reorder_level','p.is_active',
                         'c.category_name',
                         DB::raw('pv.stock_qty as stock_qty'),
                         'pv.variation_id',
                         'pv.variation_name',
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
                         'p.unit_price','p.stock_qty','p.reorder_level',
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
        $variationId   = $product->variation_id ?? null;
        $variationName = $product->variation_name ?? null;
        $variationCode = $product->variation_barcode ?? null;
        $variationSku  = $product->variation_sku ?? null;

        return response()->json([
            'status'  => 'found',
            'product' => [
                'product_id'     => $product->product_id,
                'variation_id'   => $variationId,
                'sku'            => $variationSku ?: $product->sku,
                'barcode'        => $variationCode ?: $scanCode,
                'ean13'          => $variationCode ?: $scanCode,
                'product_name'   => $product->product_name . ($variationName ? ' — ' . $variationName : ''),
                'brand'          => $product->brand,
                'unit_price'     => (float) $product->unit_price,
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

        ActivityLog::record(
            action:      'barcode_generated',
            subject:     $product->product_name,
            description: "Barcode generated for {$product->product_name} (SKU: {$product->sku}). EAN13: {$ean13}.",
            user:        Auth::user(),
        );

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
            'product_id'      => 'required|integer|exists:products,product_id',
            'variation_id'    => 'nullable|integer|exists:product_variations,variation_id',
            'action'          => 'required|in:add-existing,stock-out',
            'quantity'        => 'required|integer|min:1',
            'reference_no'    => 'nullable|string|max:60',
            'notes'           => 'nullable|string|max:255',
            'movement_reason' => 'nullable|string|max:60',
        ]);

        $product = DB::table('products')
            ->where('product_id', $request->product_id)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $qty            = (int) $request->quantity;
        $action         = $request->action;
        $movementReason = $request->movement_reason ?? null;
        $variationId    = $request->variation_id ? (int)$request->variation_id : null;

        // If a variation is specified, use the variation's own stock as $before
        if ($variationId) {
            $variation = DB::table('product_variations')
                ->where('variation_id', $variationId)
                ->lockForUpdate()
                ->first();

            if (!$variation) {
                return response()->json(['status' => 'error', 'message' => 'Variation not found.'], 404);
            }
            $before = (int) $variation->stock_qty;
        } else {
            $before = (int) $product->stock_qty;
        }

        if ($action === 'stock-out') {
            if ($qty > $before) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Not enough stock. Current: {$before}, Requested: {$qty}.",
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $after = $action === 'add-existing' ? $before + $qty : $before - $qty;

            // 1. Update stock qty
            if ($variationId) {
                // Update the specific variation's stock
                DB::table('product_variations')
                    ->where('variation_id', $variationId)
                    ->update(['stock_qty' => $after]);

                // Recalculate product stock as the sum of all active variation stocks
                $newProductStock = (int) DB::table('product_variations')
                    ->where('product_id', $product->product_id)
                    ->where('is_active', true)
                    ->sum('stock_qty');

                DB::table('products')
                    ->where('product_id', $product->product_id)
                    ->update(['stock_qty' => $newProductStock, 'updated_at' => now()]);
            } else {
                // No variation — update product stock directly
                DB::table('products')
                    ->where('product_id', $product->product_id)
                    ->update(['stock_qty' => $after, 'updated_at' => now()]);
            }

            // 2. If stock-out is a SALE -> create sales_orders + sales_order_items
            $orderNumber = null;
            if ($action === 'stock-out' && $movementReason === 'sales') {
                $nextId      = (DB::table('sales_orders')->max('order_id') ?? 0) + 1;
                $orderNumber = 'SO-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $unitPrice   = (float) $product->unit_price;
                $total       = $unitPrice * $qty;

                $orderId = DB::table('sales_orders')->insertGetId([
                    'order_number'   => $orderNumber,
                    'subtotal'       => $total,
                    'total_amount'   => $total,
                    'served_by'      => Auth::id(),
                    'status'         => 'completed',
                    'order_date'     => now('Asia/Manila')->toDateString(),
                    'created_at'  => now('Asia/Manila'),
                ], 'order_id');

                DB::table('sales_order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $product->product_id,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $total,
                ]);
            }

            // 3. Record stock movement
            $refNo = $orderNumber ?? $request->reference_no;
            $notes = $request->notes
                ?? ($action === 'add-existing'
                    ? 'Stock In via Barcode Scan'
                    : ($movementReason === 'sales'
                        ? "Sale via Barcode Scan — {$orderNumber}"
                        : 'Stock Out via Barcode Scan'));

            DB::table('stock_movements')->insert([
                'product_id'      => $product->product_id,
                'variation_id'    => $variationId,
                'movement_type'   => $action === 'add-existing' ? 'in' : 'out',
                'movement_reason' => $action === 'add-existing' ? 'restock' : $movementReason,
                'quantity'        => $qty,
                'qty_before'      => $before,
                'qty_after'       => $after,
                'reference_no'    => $refNo,
                'notes'           => $notes,
                'performed_by'    => Auth::id(),
                'movement_date'   => now('Asia/Manila')->toDateString(),
                'created_at'      => now('Asia/Manila'),
            ]);

            // 4. Log scan
            DB::table('scan_logs')->insert([
                'product_id'   => $product->product_id,
                'variation_id' => $variationId,
                'scanned_code' => $request->scanned_code ?? $product->barcode ?? $product->sku,
                'action'       => $action,
                'quantity'     => $qty,
                'performed_by' => Auth::id(),
                'scanned_at'   => now('Asia/Manila'),
            ]);

            DB::commit();

            $user        = Auth::user();
            $productName = $product->product_name ?? $product->sku;
            if ($action === 'add-existing') {
                ActivityLog::record(
                    action:      'stock_in',
                    subject:     $productName,
                    description: "Barcode stock-in: +{$qty} units. Stock: {$before} → {$after}. Ref: {$refNo}.",
                    user:        $user,
                );
            } else {
                $reason = $movementReason ? " Reason: {$movementReason}." : '';
                ActivityLog::record(
                    action:      'stock_out',
                    subject:     $productName,
                    description: "Barcode stock-out: -{$qty} units. Stock: {$before} → {$after}.{$reason} Ref: {$refNo}.",
                    user:        $user,
                );
            }

            return response()->json([
                'status'       => 'success',
                'message'      => $action === 'add-existing' ? "Added {$qty} units." : "Removed {$qty} units.",
                'qty_before'   => $before,
                'qty_after'    => $after,
                'product_id'   => $product->product_id,
                'order_number' => $orderNumber,
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
            ->leftJoin('product_variations as pv', 'sl.variation_id', '=', 'pv.variation_id')
            ->leftJoin('users as u', 'sl.performed_by', '=', 'u.user_id')
            ->select(
                'sl.log_id',
                'sl.scanned_code',
                'sl.action',
                'sl.quantity',
                'sl.scanned_at',
                'p.product_name',
                'pv.variation_name',
                DB::raw("COALESCE(pv.sku, p.sku) as sku"),
                DB::raw("COALESCE(pv.stock_qty, p.stock_qty) as stock_qty"),
                'u.username'
            )
            ->orderByDesc('sl.scanned_at')
            ->limit(50)
            ->get()
            ->map(fn($log) => [
                'log_id'       => $log->log_id,
                'scanned_code' => $log->scanned_code,
                'action'       => $log->action,
                'quantity'     => $log->quantity,
                'scanned_at'   => $log->scanned_at,
                'product_name' => $log->product_name
                    ? ($log->product_name . ($log->variation_name ? ' — ' . $log->variation_name : ''))
                    : null,
                'sku'          => $log->sku,
                'stock_qty'    => $log->stock_qty,
                'username'     => $log->username,
            ]);

        return response()->json(['status' => 'success', 'logs' => $logs]);
    }
    public function clearScanLogs(Request $request)
    {
        DB::table('scan_logs')
            ->where(function ($q) {
                $q->where('performed_by', Auth::id())
                  ->orWhereNull('performed_by');
            })
            ->delete();
        return response()->json(['status' => 'success', 'message' => 'Scan logs cleared.']);
    }

    public function logScan(Request $request)
    {
        $request->validate([
            'product_id'   => 'nullable|integer|exists:products,product_id',
            'scanned_code' => 'required|string|max:100',
            'action'       => 'required|string|max:30',
        ]);

        DB::table('scan_logs')->insert([
            'product_id'   => $request->product_id,
            'scanned_code' => $request->scanned_code,
            'action'       => $request->action,
            'quantity'     => 0,
            'performed_by' => Auth::id(),
            'scanned_at'   => now('Asia/Manila'),
        ]);

        $productName = null;
        if ($request->product_id) {
            $p = DB::table('products')->where('product_id', $request->product_id)->value('product_name');
            $productName = $p ?? $request->scanned_code;
        }
        $actionLabel = $request->action === 'not-found' ? 'Not Found' : 'Lookup';
        ActivityLog::record(
            action:      'barcode_scan',
            subject:     $productName ?? $request->scanned_code,
            description: "Barcode {$actionLabel}: scanned '{$request->scanned_code}'." . ($productName ? " Product: {$productName}." : ' Product not found.'),
            user:        Auth::user(),
        );

        return response()->json(['status' => 'success']);
    }

    public function index()
    {
        return view('barcode');
    }
}

// Auto-assign SKU as barcode for all products that have no barcode yet
$updated = 0;
$products = DB::table('products')->where('is_active', true)->get();
foreach ($products as $p) {
    // Use existing barcode if already set, else use SKU
    $code = $p->barcode ?: $p->sku;
    DB::table('products')->where('product_id', $p->product_id)->update(['barcode' => $code]);
    $updated++;
}
// Also assign variation barcodes using variation SKU or parent SKU + index
$vars = DB::table('product_variations')->where('is_active', true)->get();
foreach ($vars as $v) {
    if (!$v->barcode) {
        $parent = DB::table('products')->where('product_id', $v->product_id)->first();
        $varCode = ($v->sku ?: ($parent->sku . '-' . $v->variation_id));
        DB::table('product_variations')->where('variation_id', $v->variation_id)->update(['barcode' => $varCode]);
    }
}
echo 'Done! ' . $updated . ' products assigned.\n';
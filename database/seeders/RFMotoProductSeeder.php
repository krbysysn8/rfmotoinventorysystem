<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RFMotoProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Clear existing data
        DB::statement('SET session_replication_role = replica;');
        DB::table('product_variations')->delete();
        DB::table('products')->delete();
        DB::table('subcategories')->delete();
        DB::table('categories')->delete();
        DB::table('suppliers')->delete();
        DB::statement('SET session_replication_role = DEFAULT;');

        // Reset sequences
        DB::statement("SELECT setval(pg_get_serial_sequence('categories','category_id'), 1, false);");
        DB::statement("SELECT setval(pg_get_serial_sequence('subcategories','subcategory_id'), 1, false);");
        DB::statement("SELECT setval(pg_get_serial_sequence('suppliers','supplier_id'), 1, false);");
        DB::statement("SELECT setval(pg_get_serial_sequence('products','product_id'), 1, false);");

        // Categories
        $catIds = [];
        $catIds['Top Box'] = DB::table('categories')->insertGetId(['category_name' => 'Top Box', 'icon' => 'fa-box', 'description' => null, 'color_hex' => '#17b8dc', 'created_at' => $now, 'updated_at' => $now], 'category_id');
        $catIds['Helmet'] = DB::table('categories')->insertGetId(['category_name' => 'Helmet', 'icon' => 'fa-hard-hat', 'description' => null, 'color_hex' => '#17b8dc', 'created_at' => $now, 'updated_at' => $now], 'category_id');
        $catIds['Bracket'] = DB::table('categories')->insertGetId(['category_name' => 'Bracket', 'icon' => 'fa-wrench', 'description' => null, 'color_hex' => '#17b8dc', 'created_at' => $now, 'updated_at' => $now], 'category_id');

        // Subcategories (no timestamps)
        $subIds = [];
        $subIds['Top Box|Alloy 45L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Alloy 45L'], 'subcategory_id');
        $subIds['Top Box|Alloy 50L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Alloy 50L'], 'subcategory_id');
        $subIds['Top Box|Hard Plastic 30L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Hard Plastic 30L'], 'subcategory_id');
        $subIds['Top Box|Hard Plastic 38L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Hard Plastic 38L'], 'subcategory_id');
        $subIds['Top Box|Hard Plastic 45L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Hard Plastic 45L'], 'subcategory_id');
        $subIds['Top Box|Hard Plastic 60L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Hard Plastic 60L'], 'subcategory_id');
        $subIds['Top Box|Hard Plastic 65L'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Top Box'], 'subcategory_name' => 'Hard Plastic 65L'], 'subcategory_id');
        $subIds['Helmet|Full Face Helmet'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Helmet'], 'subcategory_name' => 'Full Face Helmet'], 'subcategory_id');
        $subIds['Helmet|Half Face Helmet'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Helmet'], 'subcategory_name' => 'Half Face Helmet'], 'subcategory_id');
        $subIds['Helmet|Modular Helmet'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Helmet'], 'subcategory_name' => 'Modular Helmet'], 'subcategory_id');
        $subIds['Bracket|Top Box Bracket'] = DB::table('subcategories')->insertGetId(['category_id' => $catIds['Bracket'], 'subcategory_name' => 'Top Box Bracket'], 'subcategory_id');

        // Suppliers
        $supIds = [];
        $supIds['HNJ'] = DB::table('suppliers')->insertGetId(['supplier_name' => 'HNJ', 'contact_person' => null, 'phone' => null, 'email' => null, 'address' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now], 'supplier_id');
        $supIds['Hexa Moto'] = DB::table('suppliers')->insertGetId(['supplier_name' => 'Hexa Moto', 'contact_person' => null, 'phone' => null, 'email' => null, 'address' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now], 'supplier_id');
        $supIds['MC'] = DB::table('suppliers')->insertGetId(['supplier_name' => 'MC', 'contact_person' => null, 'phone' => null, 'email' => null, 'address' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now], 'supplier_id');
        $supIds['SEC'] = DB::table('suppliers')->insertGetId(['supplier_name' => 'SEC', 'contact_person' => null, 'phone' => null, 'email' => null, 'address' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now], 'supplier_id');

        // Products & Variations
        // SEC 3X Antiscratch
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0001',
            'product_name'   => 'SEC 3X Antiscratch',
            'description'    => '45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3899.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000017',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0000100001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Ambassador
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0002',
            'product_name'   => 'SEC Ambassador',
            'description'    => '45L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3200.0,
            'stock_qty'      => 70,
            'reorder_level'  => 1,
            'barcode'        => '0000000000024',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 50,
            'unit_price' => 0,
            'barcode'        => '0000200001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => '',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0000200002002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Bird
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0003',
            'product_name'   => 'SEC Black Bird',
            'description'    => '45L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3599.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000031',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 30,
            'unit_price' => 0,
            'barcode'        => '0000300001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Dawn45L Alloy Dual Lock Top Box with Metal Base Plate
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0004',
            'product_name'   => 'SEC Black Dawn45L Alloy Dual Lock Top Box with Metal Base Plate',
            'description'    => '',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3700.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000048',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0000400001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Warrior
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0005',
            'product_name'   => 'SEC Black Warrior',
            'description'    => '45L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3500.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000055',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0000500001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Dictator
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0006',
            'product_name'   => 'SEC Dictator',
            'description'    => '45L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3599.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000062',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0000600001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Dictator Anti Scratch
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0007',
            'product_name'   => 'SEC Dictator Anti Scratch',
            'description'    => '45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3799.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000079',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0000700001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Extraction Anti Scratch
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0008',
            'product_name'   => 'SEC Extraction Anti Scratch',
            'description'    => '45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3799.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000086',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0000800001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Hexa Moto
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'HEX-0009',
            'product_name'   => 'Hexa Moto',
            'description'    => '45L Anti Scratch Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['Hexa Moto'],
            'brand'          => 'Hexa Moto',
            'unit_price'     => 2899.0,
            'stock_qty'      => 6,
            'reorder_level'  => 1,
            'barcode'        => '0000000000093',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 3,
            'unit_price' => 0,
            'barcode'        => '0000900001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Grey',
            'stock_qty'      => 3,
            'unit_price' => 0,
            'barcode'        => '0000900002005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Intruders
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0010',
            'product_name'   => 'SEC Intruders',
            'description'    => '45L Alloy Anti Scratch 45L Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3700.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000109',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0001000001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Tour
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0011',
            'product_name'   => 'SEC Tour',
            'description'    => '45L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3800.0,
            'stock_qty'      => 5,
            'reorder_level'  => 1,
            'barcode'        => '0000000000116',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0001100001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Ranger
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0012',
            'product_name'   => 'SEC Ranger',
            'description'    => '50L Alloy Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Alloy 50L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3500.0,
            'stock_qty'      => 80,
            'reorder_level'  => 1,
            'barcode'        => '0000000000123',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0001200001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Silver',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0001200002009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Classic
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0013',
            'product_name'   => 'SEC Classic',
            'description'    => '30L Hard Plastic Single Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 30L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1500.0,
            'stock_qty'      => 3,
            'reorder_level'  => 1,
            'barcode'        => '0000000000130',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 3,
            'unit_price' => 0,
            'barcode'        => '0001300001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Ghost
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0014',
            'product_name'   => 'SEC Black Ghost',
            'description'    => '38L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 38L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000147',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0001400001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC 1X
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0015',
            'product_name'   => 'SEC 1X',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2499.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000154',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0001500001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Adamantium v2
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0016',
            'product_name'   => 'SEC Adamantium v2',
            'description'    => '45L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2000.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000161',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0001600001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Ghost 2.0
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0017',
            'product_name'   => 'SEC Black Ghost 2.0',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2999.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000178',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0001700001007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Black Monster
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0018',
            'product_name'   => 'SEC Black Monster',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000185',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0001800001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC X Demon Slayer Gyomei
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0019',
            'product_name'   => 'SEC X Demon Slayer Gyomei',
            'description'    => '45L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 4000.0,
            'stock_qty'      => 5,
            'reorder_level'  => 1,
            'barcode'        => '0000000000192',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Yellow/Red',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0001900001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC X Demon Slayer Nezuko
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0020',
            'product_name'   => 'SEC X Demon Slayer Nezuko',
            'description'    => '45L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 4000.0,
            'stock_qty'      => 5,
            'reorder_level'  => 1,
            'barcode'        => '0000000000208',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Pink/Green',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0002000001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC X Demon Slayer Tanjiro
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0021',
            'product_name'   => 'SEC X Demon Slayer Tanjiro',
            'description'    => '45L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 4000.0,
            'stock_qty'      => 5,
            'reorder_level'  => 1,
            'barcode'        => '0000000000215',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Green/Black',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0002100001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC X Demon Slayer Wind Hashira
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0022',
            'product_name'   => 'SEC X Demon Slayer Wind Hashira',
            'description'    => '45L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 4000.0,
            'stock_qty'      => 5,
            'reorder_level'  => 1,
            'barcode'        => '0000000000222',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White/Green',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0002200001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Freedom New
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0023',
            'product_name'   => 'SEC Freedom New',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2799.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000239',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 30,
            'unit_price' => 0,
            'barcode'        => '0002300001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Freedom Old
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0024',
            'product_name'   => 'SEC Freedom Old',
            'description'    => '45L Hard Plasctic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2899.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000246',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0002400001007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // HNJ Coffer
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'HNJ-0025',
            'product_name'   => 'HNJ Coffer',
            'description'    => '45L Hard Plastic Single Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['HNJ'],
            'brand'          => 'HNJ',
            'unit_price'     => 1700.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000253',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0002500001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Legacy
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0026',
            'product_name'   => 'SEC Legacy',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2700.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000260',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 40,
            'unit_price' => 0,
            'barcode'        => '0002600001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Loot Box Number Lock
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0027',
            'product_name'   => 'SEC Loot Box Number Lock',
            'description'    => '45L Hard Plastic Dual Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2000.0,
            'stock_qty'      => 50,
            'reorder_level'  => 1,
            'barcode'        => '0000000000277',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 50,
            'unit_price' => 0,
            'barcode'        => '0002700001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Loot Box Key Lock
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0028',
            'product_name'   => 'SEC Loot Box Key Lock',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2099.0,
            'stock_qty'      => 50,
            'reorder_level'  => 1,
            'barcode'        => '0000000000284',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 50,
            'unit_price' => 0,
            'barcode'        => '0002800001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // MC Ignite
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'MC-0029',
            'product_name'   => 'MC Ignite',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['MC'],
            'brand'          => 'MC',
            'unit_price'     => 2500.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000291',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0002900001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // MC Graphene
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'MC-0030',
            'product_name'   => 'MC Graphene',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['MC'],
            'brand'          => 'MC',
            'unit_price'     => 2600.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000307',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003000001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003000002005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Modern Tech
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0031',
            'product_name'   => 'SEC Modern Tech',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000314',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0003100001007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Monster Old
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0032',
            'product_name'   => 'SEC Monster Old',
            'description'    => '45L Hard Plastic Dual Lock Top Box wtih metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2700.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000321',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0003200001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Ribs
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0033',
            'product_name'   => 'SEC Ribs',
            'description'    => '45L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 45L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2700.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000338',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0003300001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003300002002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Liberty
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0034',
            'product_name'   => 'SEC Liberty',
            'description'    => '60L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 60L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3399.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000345',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 30,
            'unit_price' => 0,
            'barcode'        => '0003400001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Secure
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0035',
            'product_name'   => 'SEC Secure',
            'description'    => '65L Hard Plastic Single Lock Top Box',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 65L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3500.0,
            'stock_qty'      => 50,
            'reorder_level'  => 1,
            'barcode'        => '0000000000352',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 50,
            'unit_price' => 0,
            'barcode'        => '0003500001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Hexalite
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0036',
            'product_name'   => 'SEC Hexalite',
            'description'    => '65L Hard Plastic Dual Lock Top Box with Metal Base Plate',
            'category_id'    => $catIds['Top Box'],
            'subcategory_id' => $subIds['Top Box|Hard Plastic 65L'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 4500.0,
            'stock_qty'      => 50,
            'reorder_level'  => 1,
            'barcode'        => '0000000000369',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 50,
            'unit_price' => 0,
            'barcode'        => '0003600001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Atmos
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0037',
            'product_name'   => 'SEC Atmos',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2800.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000376',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0003700001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Blue',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0003700002008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Challenger
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0038',
            'product_name'   => 'SEC Challenger',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3300.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000383',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003800001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003800002007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Chroma
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0039',
            'product_name'   => 'SEC Chroma',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3200.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000390',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003900001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0003900002006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Integra
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0040',
            'product_name'   => 'SEC Integra',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2950.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000406',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004000001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004000002002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004000003009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Pace
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0041',
            'product_name'   => 'SEC Pace',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3099.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000413',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004100001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004100002001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004100003008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Saga
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0042',
            'product_name'   => 'SEC Saga',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2800.0,
            'stock_qty'      => 140,
            'reorder_level'  => 1,
            'barcode'        => '0000000000420',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black Grey',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black Red',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200002000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black Red Grey',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200003007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Optic White Grey',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200004004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Plain Gloss White',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200005001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Plain Matte Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200006008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Silver Black',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0004200007005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Spots Daily
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0043',
            'product_name'   => 'SEC Spots Daily',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2400.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000437',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Pink',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0004300001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White',
            'stock_qty'      => 5,
            'unit_price' => 0,
            'barcode'        => '0004300002009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC X MESUCA Winnie the Pooh
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0044',
            'product_name'   => 'SEC X MESUCA Winnie the Pooh',
            'description'    => 'Disney Winnie the Pooh Theme Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 3,
            'reorder_level'  => 1,
            'barcode'        => '0000000000444',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Yellow/White',
            'stock_qty'      => 3,
            'unit_price' => 0,
            'barcode'        => '0004400001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Zephyr
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0045',
            'product_name'   => 'SEC Zephyr',
            'description'    => 'Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Full Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2999.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000451',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Gray',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004500001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004500002007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004500003004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Essential
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0046',
            'product_name'   => 'SEC Essential',
            'description'    => 'Half Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Half Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2699.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000468',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004600001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004600002006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004600003003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Fade
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0047',
            'product_name'   => 'SEC Fade',
            'description'    => 'Half Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Half Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2699.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000475',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004700001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004700002005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Focus
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0048',
            'product_name'   => 'SEC Focus',
            'description'    => 'Half Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Half Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2500.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000482',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004800001007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White Silver',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004800002004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Grid Duox
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0049',
            'product_name'   => 'SEC Grid Duox',
            'description'    => 'Half Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Half Face Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2300.0,
            'stock_qty'      => 20,
            'reorder_level'  => 1,
            'barcode'        => '0000000000499',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004900001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White Red Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0004900002003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Element
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0050',
            'product_name'   => 'SEC Element',
            'description'    => 'Modular (Half/Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Modular Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000505',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005000001002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Gloss White',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005000002009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Matte Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005000003006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Revolt Air
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0051',
            'product_name'   => 'SEC Revolt Air',
            'description'    => 'Modular (Half/Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Modular Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2600.0,
            'stock_qty'      => 80,
            'reorder_level'  => 1,
            'barcode'        => '0000000000512',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Candy Blue',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0005100001001',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Red Chilli',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0005100002008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Royalty Blue Red',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0005100003005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Yellow Lime',
            'stock_qty'      => 20,
            'unit_price' => 0,
            'barcode'        => '0005100004002',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Whirlwind Pista
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0052',
            'product_name'   => 'SEC Whirlwind Pista',
            'description'    => 'Modular (Half/Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Modular Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 30,
            'reorder_level'  => 1,
            'barcode'        => '0000000000529',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Silver White Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005200001000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White Black Grey',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005200002007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White Blue Red Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005200003004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Whirlwind Runner
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0053',
            'product_name'   => 'SEC Whirlwind Runner',
            'description'    => 'Modular (Half/Full Face Dual Visor Helmet',
            'category_id'    => $catIds['Helmet'],
            'subcategory_id' => $subIds['Helmet|Modular Helmet'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 3000.0,
            'stock_qty'      => 40,
            'reorder_level'  => 1,
            'barcode'        => '0000000000536',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black Grey',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005300001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black Red',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005300002006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Grey Red',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005300003003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'White Red',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005300004000',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Honda Navi DC Monorack
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0054',
            'product_name'   => 'Honda Navi DC Monorack',
            'description'    => 'Bracket for Honda Navi',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2000.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000543',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005400001008',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Q4 Rambo Nmax V3
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0055',
            'product_name'   => 'Q4 Rambo Nmax V3',
            'description'    => 'Bracket compatible for Nmax V3',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1499.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000550',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005500001007',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Titan ADV
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0056',
            'product_name'   => 'Titan ADV',
            'description'    => 'Bracket compatible for ADV 160',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 2000.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000567',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005600001006',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Metal Warrior Click
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0057',
            'product_name'   => 'Metal Warrior Click',
            'description'    => 'Bracket compatible for clink 125/150',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1800.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000574',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005700001005',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Winner X Bracket Forward
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0058',
            'product_name'   => 'Winner X Bracket Forward',
            'description'    => 'Stay Grab Bracket Compatible for Winner X',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1500.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000581',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005800001004',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // Winner X Bracket Robin
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0059',
            'product_name'   => 'Winner X Bracket Robin',
            'description'    => 'Stay Grab Bracket Compatible for Winner X',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1700.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000598',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0005900001003',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        // SEC Q4 Click
        $pid = DB::table('products')->insertGetId([
            'sku'            => 'SEC-0060',
            'product_name'   => 'SEC Q4 Click',
            'description'    => 'Stay Grab Bracket Compatible for Honda Click',
            'category_id'    => $catIds['Bracket'],
            'subcategory_id' => $subIds['Bracket|Top Box Bracket'],
            'supplier_id'    => $supIds['SEC'],
            'brand'          => 'SEC',
            'unit_price'     => 1500.0,
            'stock_qty'      => 10,
            'reorder_level'  => 1,
            'barcode'        => '0000000000604',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ], 'product_id');
        DB::table('product_variations')->insert([
            'product_id'     => $pid,
            'variation_name' => 'Black',
            'stock_qty'      => 10,
            'unit_price' => 0,
            'barcode'        => '0006000001009',
            'is_active'      => true,
            'created_at'     => $now, 'updated_at' => $now,
        ]);

        $this->command->info('Seeded: 60 products, 96 variations.');
    }
}
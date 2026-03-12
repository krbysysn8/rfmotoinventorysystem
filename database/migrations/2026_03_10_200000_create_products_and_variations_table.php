<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── PRODUCTS ─────────────────────────────────────────────
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id('product_id');
                $table->string('sku', 50)->unique();
                $table->string('barcode', 100)->nullable()->unique();
                $table->string('product_name', 200);
                $table->text('description')->nullable();
                $table->foreignId('category_id')
                      ->constrained('categories', 'category_id');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->string('brand', 100)->nullable();
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->integer('stock_qty')->default(0);
                $table->integer('reorder_level')->default(5);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── PRODUCT VARIATIONS ────────────────────────────────────
        if (!Schema::hasTable('product_variations')) {
            Schema::create('product_variations', function (Blueprint $table) {
                $table->id('variation_id');
                $table->foreignId('product_id')
                      ->constrained('products', 'product_id')
                      ->cascadeOnDelete();
                $table->string('variation_name', 100);
                $table->string('sku', 50)->nullable();
                $table->string('barcode', 100)->nullable();
                $table->decimal('unit_price', 10, 2)->nullable();
                $table->decimal('cost_price', 10, 2)->nullable();
                $table->integer('stock_qty')->default(0);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('products');
    }
};

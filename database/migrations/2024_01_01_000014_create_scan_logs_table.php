<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * scan_logs — every barcode scan is recorded here.
     * Used for: audit trail, recent scans panel, analytics.
     */
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->unsignedInteger('product_id')->nullable(); // null if product not found
            $table->string('scanned_code', 60);               // the raw code that was scanned
            $table->enum('action', ['lookup', 'add-existing', 'stock-out', 'not-found'])
                  ->default('lookup');
            $table->integer('quantity')->default(0);           // 0 for lookup-only scans
            $table->unsignedInteger('performed_by')->nullable();
            $table->timestamp('scanned_at')->useCurrent();

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->nullOnDelete();

            $table->foreign('performed_by')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->index('product_id',   'idx_sl_product');
            $table->index('performed_by', 'idx_sl_user');
            $table->index('scanned_at',   'idx_sl_time');
            $table->index('action',       'idx_sl_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};

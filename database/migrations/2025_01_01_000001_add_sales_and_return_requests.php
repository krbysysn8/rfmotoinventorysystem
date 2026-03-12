<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add missing columns to sales_orders ────────────────
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('customer_name');
            }
            if (!Schema::hasColumn('sales_orders', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('sales_orders', 'payment_method')) {
                $table->string('payment_method', 30)->default('cash')->after('total_amount');
            }
        });

        // ── 2. Create return_requests table ───────────────────────
        if (!Schema::hasTable('return_requests')) {
            Schema::create('return_requests', function (Blueprint $table) {
                $table->id('return_id');
                $table->string('return_number', 20)->unique();

                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('product_id');

                $table->unsignedInteger('quantity');
                $table->text('reason');
                $table->string('condition_type', 30); // damaged|incomplete|defective|wrong_item|other

                $table->unsignedBigInteger('requested_by')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();

                $table->string('status', 20)->default('pending'); // pending|approved|rejected

                $table->date('return_date');
                $table->timestamp('reviewed_at')->nullable();

                $table->timestamps();

                // Foreign keys
                $table->foreign('order_id')
                      ->references('order_id')->on('sales_orders')
                      ->nullOnDelete();

                $table->foreign('product_id')
                      ->references('product_id')->on('products')
                      ->cascadeOnDelete();

                $table->foreign('requested_by')
                      ->references('user_id')->on('users')
                      ->nullOnDelete();

                $table->foreign('reviewed_by')
                      ->references('user_id')->on('users')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('subtotal');
            $table->dropColumnIfExists('discount');
            $table->dropColumnIfExists('payment_method');
        });
    }
};

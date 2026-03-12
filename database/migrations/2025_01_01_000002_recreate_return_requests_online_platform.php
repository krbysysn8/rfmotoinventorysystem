<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('return_requests');

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100)->nullable();       // platform order no. e.g. SHP-123456789
            $table->unsignedBigInteger('product_id')->nullable(); // FK to products (for stock logic)
            $table->string('product_name')->nullable();        // free-text fallback / resolved from product_id
            $table->string('platform', 30);                    // shopee | tiktok | lazada | other
            $table->string('courier', 30);                     // jnt | shopee_express | flash | other
            $table->string('item_status', 10);                 // good | bad
            $table->string('bad_reason', 30)->nullable();      // defective | damaged | no_item | wrong_item
            $table->unsignedInteger('quantity')->default(1);
            $table->date('return_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('logged_by')->nullable();
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
            $table->foreign('logged_by')->references('user_id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};

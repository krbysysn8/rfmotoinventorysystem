<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // 'sales' = customer purchase, 'damaged' = defective/write-off
            // null = not applicable (stock-in, return, adjustment, etc.)
            $table->string('movement_reason', 20)
                  ->nullable()
                  ->default(null)
                  ->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('movement_reason');
        });
    }
};
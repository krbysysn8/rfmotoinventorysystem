<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add subcategory_id to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
                $table->foreign('subcategory_id')
                      ->references('subcategory_id')
                      ->on('subcategories')
                      ->nullOnDelete();
            }
        });

        // Add icon and color_hex to categories table (needed by CategoryController)
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon', 60)->nullable()->after('description');
            }
            if (!Schema::hasColumn('categories', 'color_hex')) {
                $table->string('color_hex', 7)->nullable()->after('icon');
            }
        });

        // Add subcategories table if it doesn't exist
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->bigIncrements('subcategory_id');
                $table->unsignedBigInteger('category_id');
                $table->string('subcategory_name', 100);
                $table->foreign('category_id')
                      ->references('category_id')
                      ->on('categories')
                      ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color_hex']);
        });
    }
};

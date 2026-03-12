<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon', 60)->default('fa-tag')->after('category_name');
            }
            if (!Schema::hasColumn('categories', 'color_hex')) {
                $table->string('color_hex', 10)->default('#17b8dc')->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color_hex']);
        });
    }
};

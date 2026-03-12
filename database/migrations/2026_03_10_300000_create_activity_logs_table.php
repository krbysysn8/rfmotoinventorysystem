<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Activity Logs table ───────────────────────────────────
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
            $table->string('action', 50);          // e.g. stock_in, stock_out, login, user_created
            $table->string('subject', 200)->nullable(); // item/user/supplier affected
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for common filter queries
            $table->index('action');
            $table->index('created_at');
            $table->index('user_id');
        });

        // ── Add last_login to users table (if not already present) ──
        if (!Schema::hasColumn('users', 'last_login')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login')->nullable()->after('status');
            });
        }

        // ── Add status column to users table (if not already present) ──
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->after('role');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login')) {
                $table->dropColumn('last_login');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

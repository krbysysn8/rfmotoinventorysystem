<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_log_id')->nullable(); // null = stock alert
            $table->string('type', 20);         // danger, warn, green, cyan, blue
            $table->string('icon', 40);
            $table->string('text', 500);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_cleared')->default(false);
            $table->timestamp('notified_at')->useCurrent();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('activity_log_id')->references('id')->on('activity_logs')->onDelete('cascade');

            $table->index(['user_id', 'is_cleared']);
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

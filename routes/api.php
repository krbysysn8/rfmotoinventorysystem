<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/me',       [AuthController::class, 'me']);

    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Products
    Route::get('/products',                    [ProductController::class, 'index']);
    Route::post('/products/{id}/stock-in',     [ProductController::class, 'stockIn']);
    Route::post('/products/{id}/stock-out',    [ProductController::class, 'stockOut']);

    // Admin-only product actions
    Route::middleware('role:admin')->group(function () {
        Route::post('/products',               [ProductController::class, 'store']);
        Route::put('/products/{id}',           [ProductController::class, 'update']);
    });

    // Sales
    Route::get('/sales',    [SalesController::class, 'index']);
    Route::post('/sales',   [SalesController::class, 'store']);

    // Returns
    Route::get('/returns',               [ReturnController::class, 'index']);
    Route::post('/returns',              [ReturnController::class, 'store']);
    Route::post('/returns/{id}/approve', [ReturnController::class, 'approve'])->middleware('role:admin');
    Route::post('/returns/{id}/reject',  [ReturnController::class, 'reject'])->middleware('role:admin');

    // Verify actions — admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/verify',                [VerifyController::class, 'index']);
        Route::post('/verify/{id}/approve',  [VerifyController::class, 'approve']);
        Route::post('/verify/{id}/reject',   [VerifyController::class, 'reject']);
    });
});
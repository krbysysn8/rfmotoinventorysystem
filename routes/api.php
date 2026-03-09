<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeController;

// ── Auth ──────────────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ── Protected API routes ──────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    // ── Products ──────────────────────────────────────────────
    Route::get('/categories',          [ProductController::class, 'categories']);
    Route::get('/products',            [ProductController::class, 'index']);
    Route::get('/products/{id}',       [ProductController::class, 'show']);

    // Admin-only product mutations
    Route::middleware('role:admin')->group(function () {
        Route::post  ('/products',                  [ProductController::class, 'store']);
        Route::put   ('/products/{id}',             [ProductController::class, 'update']);
        Route::delete('/products/{id}',             [ProductController::class, 'destroy']);
        Route::put   ('/products/{id}/variations',  [ProductController::class, 'updateVariations']);
    });

    // ── Barcode ───────────────────────────────────────────────
    Route::get ('/barcode/lookup',       [BarcodeController::class, 'lookup']);
    Route::get ('/barcode/products',     [BarcodeController::class, 'products']);
    Route::get ('/barcode/scan-logs',    [BarcodeController::class, 'scanLogs']);
    Route::post('/barcode/generate',     [BarcodeController::class, 'generate']);
    Route::post('/barcode/stock-update', [BarcodeController::class, 'stockUpdate']);

});

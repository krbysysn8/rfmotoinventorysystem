<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReturnController;

// ── Auth ─────────────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me',      [AuthController::class, 'me'])->middleware('auth:sanctum');

use App\Http\Controllers\ActivityLogController;

// ── Protected API routes ──────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    // Notifications (all authenticated users)
    Route::get ('/notifications',           [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/clear',     [NotificationController::class, 'clear']);

    // Dashboard & read-only inventory (all authenticated users)
    Route::get('/categories',      [CategoryController::class, 'list']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/products',        [ProductController::class, 'index']);
    Route::get('/products/{id}',   [ProductController::class, 'show']);

    // Reports (read — all authenticated users)
    Route::get('/reports/stock-movement', [ReportsController::class, 'stockMovement']);

    // Barcode
    Route::get ('/barcode/lookup',       [BarcodeController::class, 'lookup']);
    Route::get ('/barcode/products',     [BarcodeController::class, 'products']);
    Route::get   ('/barcode/scan-logs',    [BarcodeController::class, 'scanLogs']);
    Route::delete('/barcode/scan-logs',    [BarcodeController::class, 'clearScanLogs']);
    Route::post('/barcode/generate',     [BarcodeController::class, 'generate']);
    Route::post('/barcode/stock-update', [BarcodeController::class, 'stockUpdate']);

    // Sales
    Route::get ('/sales',  [SalesController::class, 'index']);
    Route::post('/sales',  [SalesController::class, 'store']);

    // Returns (all authenticated users can file & view)
    Route::get ('/returns',              [ReturnController::class, 'index']);
    Route::post('/returns',              [ReturnController::class, 'store']);
    Route::post('/returns/{id}/approve', [ReturnController::class, 'approve']);
    Route::post('/returns/{id}/reject',  [ReturnController::class, 'reject']);

    // ── Admin-only ───────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Products (write)
        Route::post  ('/products',                         [ProductController::class,  'store']);
        Route::put   ('/products/{id}',                    [ProductController::class,  'update']);
        Route::delete('/products/{id}',                    [ProductController::class,  'destroy']);
        Route::put   ('/products/{id}/variations',         [ProductController::class,  'updateVariations']);

        // Categories (write)
        Route::post  ('/categories',                       [CategoryController::class, 'store']);
        Route::put   ('/categories/{id}',                  [CategoryController::class, 'update']);
        Route::delete('/categories/{id}',                  [CategoryController::class, 'destroy']);

        // Subcategories
        Route::post  ('/categories/{id}/subcategories',    [CategoryController::class, 'storeSubcategory']);
        Route::put   ('/subcategories/{id}',               [CategoryController::class, 'updateSubcategory']);
        Route::delete('/subcategories/{id}',               [CategoryController::class, 'destroySubcategory']);

        // Suppliers
        Route::get   ('/suppliers',                        [SupplierController::class, 'list']);
        Route::post  ('/suppliers',                        [SupplierController::class, 'store']);
        Route::put   ('/suppliers/{id}',                   [SupplierController::class, 'update']);
        Route::delete('/suppliers/{id}',                   [SupplierController::class, 'destroy']);

        Route::get('/stock-history', [StockHistoryController::class, 'index']);

        // Reports (admin-only detail endpoints)
        Route::prefix('reports')->group(function () {
            Route::get('/inventory-summary', [ReportsController::class, 'inventorySummary']);
            Route::get('/low-stock',         [ReportsController::class, 'lowStock']);
            Route::get('/out-of-stock',      [ReportsController::class, 'outOfStock']);
            Route::get('/supplier-report',   [ReportsController::class, 'supplierReport']);
            Route::get('/sales-summary',     [ReportsController::class, 'salesSummary']);
        });

        // User management
        Route::get   ('/users',      [UserManagementController::class, 'index']);
        Route::post  ('/users',      [UserManagementController::class, 'store']);
        Route::put   ('/users/{id}', [UserManagementController::class, 'update']);
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);

        // Activity logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);

    });

});
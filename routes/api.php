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
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PasswordResetController;

// ── Auth ──────────────────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me',      [AuthController::class, 'me'])->middleware('auth:sanctum');

// ── Password Reset (public — no auth required) ────────────────────
// Admin forgot password from the login page
Route::post('/password/forgot', [PasswordResetController::class, 'forgot']);
// Submit new password from the reset-password page (token link)
Route::post('/password/reset',  [PasswordResetController::class, 'reset']);

// ── Protected API routes ──────────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    // Dashboard & inventory (read)
    Route::get('/categories',      [CategoryController::class, 'list']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/products',        [ProductController::class, 'index']);
    Route::get('/products/{id}',   [ProductController::class, 'show']);

    // Reports (read)
    Route::get('/reports/stock-movement', [ReportsController::class, 'stockMovement']);

    // Barcode
    Route::get   ('/barcode/lookup',       [BarcodeController::class, 'lookup']);
    Route::get   ('/barcode/products',     [BarcodeController::class, 'products']);
    Route::get   ('/barcode/scan-logs',    [BarcodeController::class, 'scanLogs']);
    Route::delete('/barcode/scan-logs',    [BarcodeController::class, 'clearScanLogs']);
    Route::post  ('/barcode/generate',     [BarcodeController::class, 'generate']);
    Route::post  ('/barcode/stock-update', [BarcodeController::class, 'stockUpdate']);
    Route::post  ('/barcode/log-scan',     [BarcodeController::class, 'logScan']);

    // Sales
    Route::get ('/sales', [SalesController::class, 'index']);
    Route::post('/sales', [SalesController::class, 'store']);

    // Returns
    Route::get   ('/returns',      [ReturnController::class, 'index']);
    Route::post  ('/returns',      [ReturnController::class, 'store']);
    Route::delete('/returns/{id}', [ReturnController::class, 'destroy']);

    // Categories (write)
    Route::post  ('/categories',                    [CategoryController::class, 'store']);
    Route::put   ('/categories/{id}',               [CategoryController::class, 'update']);
    Route::delete('/categories/{id}',               [CategoryController::class, 'destroy']);

    // Subcategories
    Route::post  ('/categories/{id}/subcategories', [CategoryController::class, 'storeSubcategory']);
    Route::put   ('/subcategories/{id}',            [CategoryController::class, 'updateSubcategory']);
    Route::delete('/subcategories/{id}',            [CategoryController::class, 'destroySubcategory']);

    // Suppliers
    Route::get   ('/suppliers',      [SupplierController::class, 'list']);
    Route::post  ('/suppliers',      [SupplierController::class, 'store']);
    Route::put   ('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    // Stock history
    Route::get('/stock-history', [StockHistoryController::class, 'index']);

    // ── Admin-only ──────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Products (write)
        Route::post  ('/products',                 [ProductController::class, 'store']);
        Route::put   ('/products/{id}',            [ProductController::class, 'update']);
        Route::delete('/products/{id}',            [ProductController::class, 'destroy']);
        Route::put   ('/products/{id}/variations', [ProductController::class, 'updateVariations']);

        // Reports (admin detail)
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

        // Password reset — admin actions for staff
        Route::post('/password/admin-send-reset', [PasswordResetController::class, 'adminSendReset']);
        Route::post('/password/admin-set',        [PasswordResetController::class, 'adminSetPassword']);

        // Activity logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);

        // Sales delete
        Route::delete('/sales/{id}', [SalesController::class, 'destroy']);
    });
});

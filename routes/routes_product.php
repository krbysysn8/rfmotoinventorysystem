<?php

// ============================================================
// routes/web.php  — Web routes (Blade views)
// ============================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

// ── Auth guard middleware ────────────────────────────────────
// All product routes require a valid Sanctum token (auth:sanctum)
// and a verified role (handled inside controller or middleware)

// Login page
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');

// Products Blade page (served as a view, data loaded via API)
Route::get('/products', function () {
    return view('products');
})->middleware('auth:sanctum')->name('products');


// ============================================================
// routes/api.php  — API routes (JSON responses)
// ============================================================

use Illuminate\Support\Facades\Route as ApiRoute;
use App\Http\Controllers\ProductController as PC;

// Public: none (all endpoints require token)

ApiRoute::middleware('auth:sanctum')->group(function () {

    // ── Categories ─────────────────────────────────────────
    ApiRoute::get('/categories', [PC::class, 'categories']);

    // ── Products ───────────────────────────────────────────
    ApiRoute::get ('/products',              [PC::class, 'index']);
    ApiRoute::get ('/products/{id}',         [PC::class, 'show']);

    // Admin-only mutations
    ApiRoute::middleware('role:admin')->group(function () {
        ApiRoute::post  ('/products',                    [PC::class, 'store']);
        ApiRoute::put   ('/products/{id}',               [PC::class, 'update']);
        ApiRoute::delete('/products/{id}',               [PC::class, 'destroy']);
        ApiRoute::put   ('/products/{id}/variations',    [PC::class, 'updateVariations']);
    });
});


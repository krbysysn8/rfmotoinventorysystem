<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportsController;

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard',       fn() => view('dashboard')      )->name('dashboard');
Route::get('/products',        fn() => view('products')       )->name('products');
Route::get('/inventory',       fn() => view('inventory')      )->name('inventory');
Route::get('/barcode',         [BarcodeController::class, 'index'])->name('barcode');
Route::get('/stock-history',   fn() => view('stock-history')  )->name('stock-history');
Route::get('/sales',           fn() => view('sales')          )->name('sales');
Route::get('/returns',         fn() => view('returns')        )->name('returns');
Route::get('/returned-items',  fn() => view('returned-items') )->name('returned-items');
Route::get('/verify',          fn() => view('verify')         )->name('verify');
Route::get('/categories',      [CategoryController::class, 'index'])->name('categories');
Route::get('/suppliers',       [SupplierController::class, 'index'])->name('suppliers');
Route::get('/reports',         [ReportsController::class, 'index'])->name('reports');
Route::get('/user-management', fn() => view('user-management'))->name('user-management');
Route::get('/activity-logs',   fn() => view('activity-logs')  )->name('activity-logs');
Route::get('/sales',   fn() => view('sales'));
Route::get('/returns', fn() => view('returns'));
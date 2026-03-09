<?php

// ═══════════════════════════════════════════════════════════════
//  RF MOTO — routes/web.php  (COMPLETE — replace your entire file)
// ═══════════════════════════════════════════════════════════════

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeController;

// ── PUBLIC: Auth ─────────────────────────────────────────────
Route::get( '/login',  [AuthController::class, 'showLogin'] )->name('login');
Route::post('/login',  [AuthController::class, 'login']     )->name('login.post');
Route::post('/logout', [AuthController::class, 'logout']    )->name('logout')->middleware('auth:sanctum');

// Redirect root → dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── PROTECTED: All Blade pages ────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/dashboard',      function () { return view('dashboard');      })->name('dashboard');
    Route::get('/products',       function () { return view('products');       })->name('products');
    Route::get('/inventory',      function () { return view('inventory');      })->name('inventory');
    Route::get('/barcode',        [BarcodeController::class, 'index']          )->name('barcode');
    Route::get('/stock-history',  function () { return view('stock-history');  })->name('stock-history');
    Route::get('/sales',          function () { return view('sales');          })->name('sales');
    Route::get('/returns',        function () { return view('returns');        })->name('returns');
    Route::get('/returned-items', function () { return view('returned-items'); })->name('returned-items');
    Route::get('/verify',         function () { return view('verify');         })->name('verify');

});

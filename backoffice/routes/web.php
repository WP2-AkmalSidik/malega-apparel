<?php

use App\Livewire\Auth\Login;
use App\Livewire\Catalog\CategoryIndex;
use App\Livewire\Catalog\ProductIndex;
use App\Livewire\Dashboard;
use App\Livewire\Inventory\InventoryIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Malega Apparel Backoffice
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated Backoffice Routes
Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    // Module 02: Catalog Management
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('/categories', CategoryIndex::class)->name('categories');
        Route::get('/products', ProductIndex::class)->name('products');
    });

    // Module 04: Inventory Management & Ledger
    Route::get('/inventory', InventoryIndex::class)->name('inventory.index');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

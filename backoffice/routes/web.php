<?php

use App\Livewire\Auth\Login;
use App\Livewire\Catalog\CategoryIndex;
use App\Livewire\Catalog\ProductIndex;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Dashboard;
use App\Livewire\Inventory\InventoryIndex;
use App\Livewire\Orders\OrderIndex;
use App\Livewire\Public\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Malega Apparel Backoffice
|--------------------------------------------------------------------------
*/

// Public Tracking Route (Accessible to customers & public)
Route::get('/track/{order_number?}', OrderTracking::class)->name('order.track');

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
        Route::get('/collections', \App\Livewire\Catalog\CollectionIndex::class)->name('collections');
        Route::get('/products', ProductIndex::class)->name('products');
        Route::get('/fabric-specs', \App\Livewire\Catalog\FabricSpecIndex::class)->name('fabric-specs');
    });

    // Module 04: Inventory Management & Ledger
    Route::get('/inventory', InventoryIndex::class)->name('inventory.index');

    // Module 05: Order & Commerce Management
    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/{order}/shipping-label', [\App\Http\Controllers\ShippingLabelController::class, 'print'])->name('orders.shipping-label');

    // Module 06: Customer Management
    Route::get('/customers', CustomerIndex::class)->name('customers.index');

    // Module 07: Finance & Treasury Management
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/payment-logs', \App\Livewire\Finance\PaymentLogsIndex::class)->name('payment-logs');
        Route::get('/cash-flow', \App\Livewire\Finance\CashFlowIndex::class)->name('cash-flow');
        Route::get('/reports', \App\Livewire\Finance\FinancialReportIndex::class)->name('reports');
    });

    // Module 08: Marketing & Voucher Engine
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/vouchers', \App\Livewire\Marketing\VoucherIndex::class)->name('vouchers');
    });

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

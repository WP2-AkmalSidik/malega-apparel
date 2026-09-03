<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Malega Apparel Storefront REST API v1
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Storefront Public REST API v1
Route::prefix('v1')->name('api.v1.')->group(function () {
    // 1. Catalog Endpoints
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

    // 2. Order & Checkout Endpoints
    Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/orders/{order_number}', [OrderController::class, 'track'])->name('orders.track');

    // 3. Payment Gateway Endpoints (Duitku)
    Route::get('/payments/methods', [\App\Http\Controllers\Api\V1\PaymentController::class, 'methods'])->name('payments.methods');
    Route::post('/payments/invoice', [\App\Http\Controllers\Api\V1\PaymentController::class, 'createInvoice'])->name('payments.invoice');
    Route::get('/payments/status/{order_number}', [\App\Http\Controllers\Api\V1\PaymentController::class, 'status'])->name('payments.status');

    // 4. Customer Authentication & Account Endpoints
    Route::post('/customers/register', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'register'])->name('customers.register');
    Route::post('/customers/login', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'login'])->name('customers.login');
    Route::get('/customers/me', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'me'])->name('customers.me');
    Route::put('/customers/profile', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'updateProfile'])->name('customers.profile');
    Route::post('/customers/wishlist', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'syncWishlist'])->name('customers.wishlist');
    Route::get('/customers/orders', [\App\Http\Controllers\Api\V1\CustomerAuthController::class, 'orders'])->name('customers.orders');

    // 5. Logistics & Webhooks
    Route::post('/webhooks/biteship', [\App\Http\Controllers\Api\V1\BiteshipWebhookController::class, 'handle'])->name('webhooks.biteship');
    Route::post('/webhooks/duitku', [\App\Http\Controllers\Api\V1\DuitkuWebhookController::class, 'handle'])->name('webhooks.duitku');
});

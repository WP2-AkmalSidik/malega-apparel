<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Duitku Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for Duitku Payment Gateway (API v2).
    | Supports Sandbox and Production environments.
    |
    */

    'merchant_code' => env('DUITKU_MERCHANT_CODE', 'D9099'),

    'api_key' => env('DUITKU_API_KEY', ''),

    'environment' => env('DUITKU_ENV', 'sandbox'), // 'sandbox' or 'production'

    'sandbox_base_url' => 'https://sandbox.duitku.com/webapi/api/merchant',

    'production_base_url' => 'https://passport.duitku.com/webapi/api/merchant',

    'callback_url' => env('DUITKU_CALLBACK_URL', 'https://malega.my.id/api/v1/webhooks/duitku'),

    'return_url' => env('DUITKU_RETURN_URL', 'https://store.malega.my.id/order-confirmation'),

    'expiry_period' => (int) env('DUITKU_EXPIRY_MINUTES', 1440), // 24 hours in minutes

];

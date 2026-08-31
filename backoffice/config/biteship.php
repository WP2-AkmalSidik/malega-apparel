<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biteship Logistics API Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('BITESHIP_API_KEY', ''),

    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | Origin / Warehouse Default Details
    |--------------------------------------------------------------------------
    | Default sender address for fulfillment pickup and rate calculation.
    */
    'origin' => [
        'contact_name' => env('BITESHIP_ORIGIN_NAME', 'Malega Apparel Warehouse'),
        'contact_phone' => env('BITESHIP_ORIGIN_PHONE', '081234567890'),
        'address' => env('BITESHIP_ORIGIN_ADDRESS', 'Jl. Kemang Raya No. 10, Jakarta Selatan'),
        'note' => env('BITESHIP_ORIGIN_NOTE', 'Gudang Utama Malega Apparel'),
        'postal_code' => (int) env('BITESHIP_ORIGIN_POSTAL_CODE', 12430),
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Courier Codes
    |--------------------------------------------------------------------------
    */
    'couriers' => [
        'jne' => 'JNE Express',
        'sicepat' => 'SiCepat Ekspres',
        'jnt' => 'J&T Express',
        'anteraja' => 'AnterAja',
        'ninja' => 'Ninja Xpress',
        'lion' => 'Lion Parcel',
        'pos' => 'POS Indonesia',
        'gojek' => 'GoSend (Instant)',
        'grab' => 'GrabExpress (Instant)',
    ],
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Midtrans Payment Gateway (Snap & Core API).
    | Dapatkan credential di: https://dashboard.midtrans.com
    |
    | Install SDK: composer require midtrans/midtrans-php
    |
    */

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
        'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds'        => env('MIDTRANS_IS_3DS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | BNI Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk BNI Direct API (Virtual Account).
    | Dapatkan credential di developer portal BNI.
    |
    | Install SDK: composer require bni-api/bni-php
    |
    */

    'bni' => [
        'client_id'     => env('BNI_CLIENT_ID', ''),
        'client_secret' => env('BNI_CLIENT_SECRET', ''),
        'api_key'       => env('BNI_API_KEY', ''),
        'app_name'      => env('BNI_APP_NAME', ''),
        'is_production' => env('BNI_IS_PRODUCTION', false),
    ],

];

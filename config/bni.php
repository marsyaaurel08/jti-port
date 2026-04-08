<?php

return [
    // BNI API Environment: 'sandbox' or 'prod'
    'environment' => env('BNI_ENVIRONMENT', 'sandbox'),

    // BNI API Credentials
    'client_id' => env('BNI_CLIENT_ID'),
    'client_secret' => env('BNI_CLIENT_SECRET'),
    'api_key' => env('BNI_API_KEY', ''),
    'api_secret' => env('BNI_API_SECRET', ''),

    // BNI API Endpoints
    'snap_base_url' => env('BNI_SNAP_BASE_URL', 'https://app.sandbox.midtrans.com/snap/v1'),
    'ogp_base_url' => env('BNI_OGP_BASE_URL', 'https://sandbox.bni.co.id/api/v1'),
    'bnidirect_base_url' => env('BNI_BNIDIRECT_BASE_URL', 'https://sandbox.bni.co.id/api/v1'),

    // Webhook Configuration
    'webhook_url' => env('BNI_WEBHOOK_URL', env('APP_URL') . '/api/payments/bni-callback'),
];

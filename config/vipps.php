<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vipps API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Vipps/MobilePay API integration
    |
    */

    'client_id' => env('VIPPS_CLIENT_ID'),
    'client_secret' => env('VIPPS_CLIENT_SECRET'),
    'merchant_serial_number' => env('VIPPS_MERCHANT_SERIAL_NUMBER'),
    'subscription_key' => env('VIPPS_SUBSCRIPTION_KEY'),
    'currency' => env('VIPPS_CURRENCY', 'NOK'),
    'api_url' => env('VIPPS_API_URL', 'https://apitest.vipps.no'), // Use https://api.vipps.no for production
    'return_url' => env('VIPPS_RETURN_URL'),
    'webhook_id' => env('VIPPS_WEBHOOK_ID'),
    'webhook_secret' => env('VIPPS_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | System Information
    |--------------------------------------------------------------------------
    |
    | Information about the system using the Vipps API
    |
    */
    'system' => [
        'name' => env('APP_NAME', 'Laravel'),
        'version' => env('APP_VERSION', '1.0.0'),
        'plugin_name' => 'Wirement-Vipps',
        'plugin_version' => '1.0.0',
    ],
];

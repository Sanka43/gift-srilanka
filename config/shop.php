<?php

return [
    'delivery_charge' => (float) env('DELIVERY_CHARGE', 350),

    /*
    |--------------------------------------------------------------------------
    | Maps
    |--------------------------------------------------------------------------
    | With no Google keys the site uses Leaflet + OpenStreetMap for the map and
    | OSRM for road distance. Set the browser key (Maps JavaScript + Places) to
    | switch the map to Google, and the server key (Routes API) to switch
    | distance calculation to Google.
    */
    'maps' => [
        'google_browser_key' => env('GOOGLE_MAPS_BROWSER_KEY', ''),
        'google_server_key' => env('GOOGLE_MAPS_SERVER_KEY', ''),
        'osrm_url' => env('OSRM_URL', 'https://router.project-osrm.org'),
        // Straight-line distance multiplier used only if the routing API fails.
        'fallback_road_factor' => 1.3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed delivery districts (for checkout validation)
    |--------------------------------------------------------------------------
    */
    'allowed_districts' => array_map('trim', explode(',', env('ALLOWED_DISTRICTS', 'Colombo'))),

    /*
    |--------------------------------------------------------------------------
    | Bank transfer details for manual checkout payments
    |--------------------------------------------------------------------------
    */
    'bank_transfer' => [
        'bank' => env('BANK_NAME', ''),
        'branch' => env('BANK_BRANCH', ''),
        'account_name' => env('BANK_ACCOUNT_NAME', ''),
        'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | PayHere Payment Gateway Settings
    |--------------------------------------------------------------------------
    */
    'payhere' => [
        'merchant_id' => env('PAYHERE_MERCHANT_ID', ''),
        'merchant_secret' => env('PAYHERE_MERCHANT_SECRET', ''),
        'sandbox' => filter_var(env('PAYHERE_SANDBOX', false), FILTER_VALIDATE_BOOLEAN),
        'currency' => env('PAYHERE_CURRENCY', 'LKR'),
    ],
];

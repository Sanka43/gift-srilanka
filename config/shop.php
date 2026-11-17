<?php

return [
    'delivery_charge' => (float) env('DELIVERY_CHARGE', 350),

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
        'bank' => env('BANK_NAME', 'NDB Bank'),
        'branch' => env('BANK_BRANCH', 'Rathmalana'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Southern Turn Networks'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '115511287806'),
    ],
];

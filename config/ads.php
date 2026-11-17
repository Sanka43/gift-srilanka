<?php

return [
    'enabled' => env('ADS_ENABLED', true),
    'path' => storage_path('app/advertisements.json'),
    // Fallback messages when advertisements.json is missing/empty on host.
    'messages' => array_values(array_filter(array_map(
        'trim',
        explode('|', (string) env('ADS_MESSAGES', 'Free delivery for selected Colombo areas|Same-day delivery available|Fresh flowers, cakes and premium gift hampers'))
    ))),
];

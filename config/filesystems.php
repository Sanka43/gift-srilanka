<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'public_root_mode' => env('FILESYSTEM_PUBLIC_ROOT'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            // Use public disk root when symlink is not possible on host (e.g. "storage link already exists" as folder)
            'root' => env('FILESYSTEM_PUBLIC_ROOT') === 'public'
                ? public_path('storage')
                : storage_path('app/public'),
            'url' => rtrim((string) env('APP_URL'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];

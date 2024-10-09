<?php

return [
    'channels' => [
        'fb-webhook' => [
            'driver' => 'single',
            'path' => storage_path('logs/fb-webhook.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'helpscout-webhook' => [
            'driver' => 'single',
            'path' => storage_path('logs/helpscout-webhook.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
    ]
];

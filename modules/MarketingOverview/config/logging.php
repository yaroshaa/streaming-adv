<?php

return [
    'channels' => [
        'marketing-overview' => [
            'driver' => 'single',
            'path' => storage_path('logs/marketing-overview.log'),
            'level' => env('MARKETING_LOG_LEVEL', 'debug'),
        ],
    ]
];

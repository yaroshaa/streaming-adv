<?php

$pathToWebhooksConfig = __DIR__ . '/' . 'helpscout_config.json';

return [
    'key' =>  env('HELPSCOUT_SECRET', '1234567890123456789012345678901234567890'),
    'remote_source_id' => env('HELPSCOUT_REMOTE_SOURCE_ID', 5),
    'webhooks' => file_exists($pathToWebhooksConfig) ? json_decode(file_get_contents($pathToWebhooksConfig), true) : [],
];

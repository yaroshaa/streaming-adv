<?php

$snapchatConfigPath = 'marketing_snapchat.json';
$googleConfigPath = 'marketing_google.json';
$facebookConfigPath = 'marketing_facebook.json';
$adFormConfigPath = 'marketing_ad_form.json';
$performissionConfigPath = 'marketing_performission.json';

$getJsonConfig = fn($path) => file_exists(__DIR__ . '/' . $path)
    ? json_decode(file_get_contents(__DIR__ . '/' . $path), true)
    : [];

return [
    'enabled' => env('MARKETING_SYNC_ENABLED', true),
    'debug_query' => env('MARKETING_DEBUG_QUERY', false),
    'debug_data' => env('MARKETING_DEBUG_DATA', false),
    'snapchat' => $getJsonConfig($snapchatConfigPath),
    'facebook' => $getJsonConfig($facebookConfigPath),
    'google' => $getJsonConfig($googleConfigPath),
    'adform' => $getJsonConfig($adFormConfigPath),
    'performission' => $getJsonConfig($performissionConfigPath),
];

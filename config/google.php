<?php

return [
    'config_file' => env('GOOGLE_AUTH_CONFIG_FILE', '../google_api_config.json'),
    'accepted_domains' => explode(',', env('GOOGLE_ACCEPTED_DOMAINS')),
    'api_key' => env('GOOGLE_API_KEY'),
    'geoapi_url' => env('GOOGLE_GEOAPI_URL'),
];

<?php

$pathToFeedbacksApiConfig = __DIR__ . '/'. 'facebook_applications_config.json';
$pathToWebhooksConfig = __DIR__ . '/'. 'facebook_webhooks_config.json';

return [
    'url' => env('FB_URL'),
    'token' => env('FB_TOKEN'),
    'params' => [
        'limit' => 1000,
        'level' => 'campaign',
        'fields' => [
            'account_name',
            'website_ctr',
            'website_purchase_roas',
            'cost_per_outbound_click',
            'frequency',
            'campaign_name',
            'actions',
            'action_values',
            'cpm',
            'spend',
            'impressions',
            'reach'
        ]
    ],
    /*
     * For processing feedbacks from related ad accounts via api
     */
    'applications' => file_exists($pathToFeedbacksApiConfig) ? json_decode(file_get_contents($pathToFeedbacksApiConfig), true) : [],
    /**
     * For processing webhooks
     */
    'webhooks' => file_exists($pathToWebhooksConfig) ? json_decode(file_get_contents($pathToWebhooksConfig), true) : [],
];

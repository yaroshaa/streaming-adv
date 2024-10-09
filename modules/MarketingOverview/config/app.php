<?php

use Modules\MarketingOverview\Providers\EventServiceProvider;
use Modules\MarketingOverview\Providers\MarketingOverviewProvider;
use Modules\Analytic\Providers\AnalyticEventServiceProvider;

return [
    'providers' => [
        MarketingOverviewProvider::class,
        EventServiceProvider::class,
        AnalyticEventServiceProvider::class
    ],
    'aliases' => [
    ]
];

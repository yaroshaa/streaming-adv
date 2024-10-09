<?php

use Modules\Feedbacks\Facades\Facebook;
use Modules\Feedbacks\Facades\HelpScout;
use Modules\Feedbacks\Providers\FacebookServiceProvider;
use Modules\Feedbacks\Providers\FeedbacksProvider;
use Modules\Feedbacks\Providers\HelpScoutServiceProvider;

return [
    'providers' => [
        FeedbacksProvider::class,

        FacebookServiceProvider::class,
        HelpScoutServiceProvider::class,
    ],
    'aliases' => [
        'Facebook' => Facebook::class,
        'HelpScout' => HelpScout::class,
    ]
];

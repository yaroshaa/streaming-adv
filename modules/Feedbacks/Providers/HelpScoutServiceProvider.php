<?php

namespace Modules\Feedbacks\Providers;

use Modules\Feedbacks\ClickHouse\Services\FeedbackService;
use Modules\Feedbacks\Services\HelpScout\HelpScoutService;
use Modules\Feedbacks\Services\HelpScout\HelpScoutWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

/**
 * Class HelpScoutServiceProvider
 * @package Modules\Feedbacks\Providers
 */
class HelpScoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HelpScoutService::class, function () {
            return new HelpScoutService(
                config('helpscout')
            );
        });

        /**
         * Webhooks implementation for Helpscout
         */
        $this->app->bind(HelpScoutWebhookService::class, function ($app) {
            /** @var Request $request */
            $request = $app->make(Request::class);
            return new HelpScoutWebhookService($app->make(FeedbackService::class), $request->all());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

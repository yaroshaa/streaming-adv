<?php

namespace Modules\Feedbacks\Providers;

use Illuminate\Http\Request;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;
use Modules\Feedbacks\Services\Facebook\FacebookService;
use Modules\Feedbacks\Services\Facebook\FacebookWebhookService;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * CLI implementation of processing feedbacks from FB api
         */
        $this->app->singleton(FacebookService::class, function ($app) {
            return new FacebookService(
                $app->make(EntityManager::class),
                $app->make(FeedbackService::class),
                config('fb')
            );
        });

        /**
         * Webhooks implementation of processing all entities from FB webhooks
         */
        $this->app->bind(FacebookWebhookService::class, function ($app) {
            $request = $app->make(Request::class);
            return new FacebookWebhookService($request->input('object'), $request->input('entry'), config('fb'));
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

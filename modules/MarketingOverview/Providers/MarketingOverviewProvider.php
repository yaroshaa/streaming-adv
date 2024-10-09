<?php

namespace Modules\MarketingOverview\Providers;

use Modules\MarketingOverview\ClickHouse\Repositories\MarketingExpenseRepository;
use Modules\MarketingOverview\Commands\AdForm\CheckConfigCommand;
use Modules\MarketingOverview\Commands\PerformissionAdsSyncCommand;
use Modules\MarketingOverview\Commands\Snapchat\SnapchatClearCommand;
use Modules\MarketingOverview\Commands\Snapchat\SnapchatCredentialsCommand;
use Modules\MarketingOverview\Commands\Snapchat\SnapchatGetTokenCommand;
use Modules\MarketingOverview\Commands\AdForm\CleanUpCommand;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\MarketingOverview\Commands\AdFormAdsSyncCommand;
use Modules\MarketingOverview\Commands\FacebookAdsSyncCommand;
use Modules\MarketingOverview\Commands\GoogleAdsSyncCommand;
use Modules\MarketingOverview\Commands\SnapchatAdsSyncCommand;

class MarketingOverviewProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MarketingOverviewSyncService::class, function ($app) {
            return new MarketingOverviewSyncService(
                config('marketing'),
                $app->make(MarketingExpenseRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->mergeConfigFrom(__DIR__ . '/../config/marketing.php', 'marketing');

        $this->commands([
            AdFormAdsSyncCommand::class,
            CheckConfigCommand::class,
            CleanUpCommand::class,
            FacebookAdsSyncCommand::class,
            SnapchatAdsSyncCommand::class,
            GoogleAdsSyncCommand::class,
            PerformissionAdsSyncCommand::class,
            SnapchatGetTokenCommand::class,
            SnapchatCredentialsCommand::class,
            SnapchatClearCommand::class
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('marketing-overview:facebook:sync')->everyFiveMinutes(); // Facebook update data every 5 minutes
            $schedule->command('marketing-overview:snapchat:sync')->everyFifteenMinutes(); // Snapchat update data every 15 minutes
            $schedule->command('marketing-overview:google-ads:sync')->everyMinute(); // Google update data every 1 - n  minutes
            // https://api.adform.com/help/guides/system-limits/throttling-rules 10 per minute 500 per day
            // ~ 5 markets
            $schedule->command('marketing-overview:ad-form:sync')->everyFifteenMinutes();
            $schedule->command('marketing-overview:performission:sync')->everyFifteenMinutes(); // very few transactions per day  @todo
        });
    }
}

<?php

namespace Modules\Feedbacks\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Feedbacks\Commands\Sync\FacebookCommand;
use Modules\Feedbacks\Commands\GenerateCommand;

class FeedbacksProvider extends ServiceProvider
{
    public function register()
    {
       //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->mergeConfigFrom(__DIR__ . '/../config/helpscout.php', 'helpscout');
        $this->mergeConfigFrom(__DIR__ . '/../config/fb.php', 'fb');
        $this->commands([
            GenerateCommand::class,
            FacebookCommand::class
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('feedbacks:sync:facebook')->hourly();
        });
    }
}

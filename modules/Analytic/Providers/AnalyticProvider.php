<?php

namespace Modules\Analytic\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class AnalyticProvider extends ServiceProvider
{
    public function register()
    {
       //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->mergeConfigFrom(__DIR__ . '/../config/analytic.php', 'analytic');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {

        });
    }
}

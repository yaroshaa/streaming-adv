<?php

namespace Modules\ModuleName\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\ModuleName\Commands\IAmCommand;

class ModuleNameProvider extends ServiceProvider
{
    public function register()
    {
       //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->mergeConfigFrom(__DIR__ . '/../config/modulename.php', 'modulename');

        $this->commands([
            IAmCommand::class
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            //$schedule->command('module-name:iam')->everyFiveMinutes();
        });
    }
}

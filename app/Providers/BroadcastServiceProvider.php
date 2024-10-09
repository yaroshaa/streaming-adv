<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        $this->registerChannelsFromModules();

        require base_path('routes/channels.php');
    }

    private function registerChannelsFromModules()
    {
        $channels = (new Finder())
            ->in(base_path('modules/*/routes'))
            ->name('channels.php')
            ->files();

        foreach ($channels as $channel) {
            require $channel;
        }
    }
}

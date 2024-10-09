<?php

namespace Modules\StoreToday\Providers;

use Illuminate\Support\ServiceProvider;

class StoreTodayProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

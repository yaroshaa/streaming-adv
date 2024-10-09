<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

class OrderProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

<?php

namespace Modules\Api\Providers;

use Illuminate\Support\ServiceProvider;

class ApiProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

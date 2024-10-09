<?php

namespace Modules\CompanyOverview\Providers;

use Illuminate\Support\ServiceProvider;

class CompanyOverviewProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

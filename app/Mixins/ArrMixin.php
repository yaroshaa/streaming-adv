<?php

namespace App\Mixins;

use Closure;
use Symfony\Component\Finder\Finder;

class ArrMixin
{
    public function mergeConfigFromModules(): Closure
    {
        return function(string $configName, array $mainConfig) {
            $dashboardsConfig = [];
            foreach (
                (new Finder())
                    ->in(base_path('modules/*/config/'))
                    ->name($configName . '.php')
                    ->files() as $config
            ) {
                $dashboardsConfig[] = require($config);
            }

            return array_merge_recursive($mainConfig, array_merge_recursive(...$dashboardsConfig));
        };
    }
}

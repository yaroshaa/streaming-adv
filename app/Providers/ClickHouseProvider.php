<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ClickHouseDB\Client;
use App\Vendor\ClickHouseDB\Client as ClickHouseClient;

class ClickHouseProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            $config = [
                'host' => config('clickhouse.host'),
                'port' => config('clickhouse.port'),
                'username' => config('clickhouse.username'),
                'password' => config('clickhouse.password'),
            ];
            $db = new ClickHouseClient($config);

            $db->database(config('clickhouse.dbname'));

            return $db;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}

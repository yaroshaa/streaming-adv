<?php

namespace Modules\MarketingOverview\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\MarketingOverview\Events\MarketingOverviewModifiedEvent;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen([
            'ClickHouse.Insert.MarketingExpense',
            'ClickHouse.Insert.CartActions',
            'ClickHouse.Insert.ActiveUsers',
            'ClickHouse.Insert.WarehouseStatistic',
            'Doctrine.Insert.Order',
            'Doctrine.Update.Order',
        ], function() {
            MarketingOverviewModifiedEvent::broadcast();
        });
    }
}

<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\MarketingOverview\Events\MarketingOverviewModifiedEvent;

Broadcast::channel(MarketingOverviewModifiedEvent::CHANNEL, function () {
    return true;
});

<?php

use Modules\Orders\Events\OrderUpdated;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(OrderUpdated::ORDER_CHANNEL, function () {
    return true;
});

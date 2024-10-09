<?php
use Illuminate\Support\Facades\Broadcast;
use Modules\Analytic\Events\AnalyticEvent;

/// Register broadcast events
Broadcast::channel(AnalyticEvent::CHANNEL, function () {
    return true;
});

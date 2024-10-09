<?php

namespace Modules\MarketingOverview\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarketingOverviewModifiedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public const CHANNEL = 'marketing-overview-channel';
    public function broadcastOn(): Channel
    {
       return new Channel(self::CHANNEL);
    }

    public function broadcastAs(): string
    {
        return 'marketing-overview.modified';
    }
}

<?php

namespace Modules\Analytic\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalyticEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public const CHANNEL = 'analytic-channel';

    public array $data = [];

    /**
     * AnalyticEvent constructor.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function broadcastOn(): Channel
    {
       return new Channel(self::CHANNEL);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'analytic.track';
    }
}

<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Orders\Http\Resources\OrderResource;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public const ORDER_CHANNEL = 'order-channel';

    public array $order;

    public function __construct($order)
    {
        $this->order = (new OrderResource($order))->resolve();
    }

    public function broadcastOn(): Channel
    {
        return new Channel(self::ORDER_CHANNEL);
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}

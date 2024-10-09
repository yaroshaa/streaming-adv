<?php

namespace Modules\Feedbacks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\Feedbacks\Http\Resources\FeedbackResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class FeedbackAdded implements ShouldBroadcast
{
    use SerializesModels, Dispatchable;

    public const FEEDBACK_CHANNEL = 'feedback-channel';

    public FeedbackResource $feedback;

    public function __construct(FeedbackResource $feedback)
    {
        $this->feedback = $feedback;
    }

    public function broadcastOn(): Channel
    {
        return new Channel(self::FEEDBACK_CHANNEL);
    }

    public function broadcastAs(): string
    {
        return 'feedback.added';
    }
}

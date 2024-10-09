<?php


use Illuminate\Support\Facades\Broadcast;
use Modules\Feedbacks\Events\FeedbackAdded;

Broadcast::channel(FeedbackAdded::FEEDBACK_CHANNEL, function () {
    return true;
});

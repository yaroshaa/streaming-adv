<?php

namespace Modules\Feedbacks\Services\Facebook\Webhooks;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;

/**
 * Class PageWebhook
 * @package Modules\Feedbacks\Services\Facebook\Webhooks
 */
class PageWebhook extends BaseWebhook implements VerbInterface
{
    public static array $availableVerbs = [
        VerbInterface::VERB_ADD
    ];

    public static array $availableItems = [
        ItemInterface::ITEM_COMMENT
    ];

    private FeedbackService $feedbackService;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->feedbackService = resolve(FeedbackService::class);
    }

    protected function processChange(string $entryId, array $changes): void
    {
        try {
            $message = $changes['message'] ?? '';

            $feedback = [
                'name' => $changes['from']['name'] ?? 'No name',
                'message' => $message,
                'market_id' => Arr::get($this->config, 'feedback_pages.' . $entryId . '.market_remote_id', 0),
                'source_id' => Arr::get($this->config, 'feedback_pages.' . $entryId . '.source_remote_id', 0),
                'created_at' => Carbon::createFromTimestamp($changes['created_time'])->toDateTimeLocalString(),
                'url' => Arr::get($changes, 'post.permalink_url', ''),
            ];

            $this->feedbackService->saveAndBroadcast($feedback);
            $this->getLog()->info(sprintf('Message added: "%s"', $message));
        } catch (Exception $exception) {
            $this->getLog()->error($exception->getMessage());
        }
    }
}

<?php

namespace Modules\Feedbacks\Services\HelpScout;

use Exception;
use Carbon\Carbon;
use HelpScout;
use Illuminate\Support\Arr;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;
use Psr\Log\LoggerInterface;

/**
 * Class HelpScoutWebhookService
 * @package Modules\Feedbacks\Services\HelpScout
 */
class HelpScoutWebhookService
{
    private FeedbackService $feedbackService;

    private array $payload;

    private LoggerInterface $log;

    public function __construct(FeedbackService $feedbacksService, array $payload)
    {
        $this->feedbackService = $feedbacksService;
        $this->payload = $payload;
        $this->log = HelpScout::getLogger();
    }

    public function process()
    {
        $payload = $this->payload;
        $createdBy = Arr::get($payload, 'createdBy', []);
        try {
            $message = Arr::get($payload, '_embedded.threads.0.body', Arr::get($payload, 'preview', ''));
            $this->feedbackService->saveAndBroadcast([
                'name' => implode(' ', Arr::only($createdBy, ['first', 'last'])),
                'message' => $message,
                'url' => Arr::get($payload, '_links.web.href', ''),
                'source_id' => HelpScout::getSourceId(),
                'market_id' => HelpScout::getMarketByMailboxId($payload['mailboxId']),
                'created_at' =>  Carbon::make($payload['createdAt'])->toDateTimeLocalString()
            ]);
            $this->log->info(sprintf('Message added: "%s"', $message));
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
        }
    }
}

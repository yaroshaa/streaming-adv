<?php

namespace Modules\Feedbacks\Services\Facebook\Api;

use App\Entities\Market;
use App\Entities\Source;
use Facebook;
use Carbon\Carbon;
use Exception;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\CommentFields;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;

class FeedbackProcessService
{
    const CACHE_ADS_TTL = 60 * 60 * 2;

    private string $account;
    private Source $source;
    private Market $market;
    private FeedbackService $feedbackService;

    /**
     * FeedbackProcessService constructor.
     * @param string $account
     * @param Source $source
     * @param Market $market
     * @param FeedbackService $feedbacksService
     */
    public function __construct(
        string $account,
        Source $source,
        Market $market,
        FeedbackService $feedbacksService
    ) {
        $this->account = $account;
        $this->source = $source;
        $this->market = $market;
        $this->feedbackService = $feedbacksService;
    }

    public function process()
    {
        $adsCollection = Facebook::getFacebookDataProvider()->setPath('/' . Str::of($this->account)->prepend('act_') . '/ads')
            ->setFields([
                AdFields::CREATIVE . '{effective_object_story_id}',
                AdFields::NAME
            ])->setParams([
                AdFields::EFFECTIVE_STATUS => [Ad::STATUS_ACTIVE],
            ])->withCache(self::CACHE_ADS_TTL)->getData();

        $commentProvider = Facebook::getFacebookDataProvider();
        $commentProvider->setFields([
            CommentFields::ID,
            CommentFields::CREATED_TIME,
            CommentFields::MESSAGE,
        ])->setParams([
            'since' => Carbon::now()->subDay()->timestamp,
            'filter' => 'stream'
        ]);

        foreach ($adsCollection as $ads) {
            $storyId = $ads['creative']['effective_object_story_id'];
            try {
                $commentProvider->setNext(null)->setPath('/' . $storyId . '/comments');
                foreach ($commentProvider->getData() as $comment) {
                    $message = $comment['message'];

                    try {
                        $feedback = [
                            'name' => 'No name',
                            'message' => $message,
                            'source_id' => $this->source->getRemoteId(),
                            'market_id' => $this->market->getRemoteId(),
                            'created_at' => $comment['created_time'],
                            'url' => $this->getLinkToWeb($storyId),
                        ];

                        $this->feedbackService->saveAndBroadcast($feedback);

                        Log::info('Message added' . $message);
                    } catch (Exception $exception) {
                        Log::error($exception->getMessage());
                    }
                }
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
                return;
            }
        }
    }

    /**
     * @param string $storyId
     * @return string
     */
    private function getLinkToWeb(string $storyId): string
    {
        return 'https://facebook.com/' . $storyId;
    }
}

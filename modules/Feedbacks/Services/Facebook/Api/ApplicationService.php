<?php

namespace Modules\Feedbacks\Services\Facebook\Api;

use App\ClickHouse\ClickHouseException;
use App\Entities\Market;
use App\Entities\Source;
use Facebook;
use Doctrine\ORM\EntityManager;
use FacebookAds\Api;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;

/**
 * Class ApplicationService
 * @package Modules\Feedbacks\Services\Facebook
 */
class ApplicationService
{
    const GRAPH_VERSION = '10.0';
    const CACHE_ACCOUNTS_TTL = 60 * 60 * 3;

    private int $id;

    private string $secret;

    private string $token;

    private Source $source;

    private Market $market;

    private EntityManager $entityManager;

    private FeedbackService $feedbackService;

    private Api $api;

    /**
     * FacebookAccountService constructor.
     * @param EntityManager $entityManager
     * @param FeedbackService $feedbacksService
     * @param array $config
     */
    public function __construct(
        EntityManager $entityManager,
        FeedbackService $feedbacksService,
        array $config = []
    ) {
        $this->id = $config['id'];
        $this->secret = $config['secret'];
        $this->token = $config['token'];

        $this->entityManager = $entityManager;
        $this->feedbackService = $feedbacksService;

        $this->market = $entityManager->getRepository(Market::class)->findOneBy(['remoteId' => $config['market_remote_id']]);
        $this->source = $entityManager->getRepository(Source::class)->findOneBy(['remoteId' => $config['source_remote_id']]);

        $this->api = Api::init($this->id, $this->secret, $this->token);
        $this->api->setDefaultGraphVersion(self::GRAPH_VERSION);
    }

    public function processFeedbacks()
    {
        try {
            foreach ($this->getAdAccountsAsGenerator() as $adAccount) {
                /** @var FeedbackProcessService $adAccount */
                $adAccount->process();
            }
        } catch (ClickHouseException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @return Generator
     */
    private function getAdAccountsAsGenerator(): Generator
    {
        $accounts = Arr::pluck(Facebook::getFacebookDataProvider()
            ->setPath('/' . $this->id . '/authorized_adaccounts')
            ->withCache(self::CACHE_ACCOUNTS_TTL)
            ->getData(), 'account_id');

        foreach ($accounts as $account) {
            yield new FeedbackProcessService($account, $this->source, $this->market, $this->feedbackService);
        }
    }
}

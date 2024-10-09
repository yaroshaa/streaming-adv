<?php

namespace Modules\Feedbacks\Services\Facebook;

use Modules\Feedbacks\ClickHouse\Services\FeedbackService;
use Modules\Feedbacks\Services\Facebook\Api\DataProvider;
use Modules\Feedbacks\Services\Facebook\Api\ApplicationService;
use Doctrine\ORM\EntityManager;
use FacebookAds\Api;
use Generator;

/**
 * Class FacebookService
 * @package Modules\Feedbacks\Services\Facebook
 */
class FacebookService
{
    private array $config = [];

    private EntityManager $entityManager;

    private FeedbackService $feedbacksService;

    /**
     * FacebookService constructor.
     * @param EntityManager $entityManager
     * @param FeedbackService $feedbacksService
     * @param array $config
     */
    public function __construct(
        EntityManager $entityManager,
        FeedbackService $feedbacksService,
        array $config = []
    )
    {
        $this->config = array_replace($this->config, $config);
        $this->entityManager = $entityManager;
        $this->feedbacksService = $feedbacksService;
    }

    public function getApplicationServicesAsGenerator(): Generator
    {
        foreach ($this->config['applications'] as $application) {
            yield new ApplicationService($this->entityManager, $this->feedbacksService, $application);
        }
    }

    public function getFacebookDataProvider(): DataProvider
    {
        return new DataProvider(Api::instance());
    }
}

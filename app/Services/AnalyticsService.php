<?php

namespace App\Services;

use App\ClickHouse\ClickhouseConfig;
use App\ClickHouse\Models\AnalyticsEventProperties;
use App\ClickHouse\Models\AnalyticsEvents;
use App\Entities\AnalyticsSite;
use ClickHouseDB\Client;
use Doctrine\ORM\EntityManager;

/**
 * Class AnalyticsService
 * @package App\Services
 */
class AnalyticsService
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var Client
     */
    private Client $clickhouse;

    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $clickhouseConfig;

    /**
     * AnalyticsService constructor.
     * @param EntityManager $entityManager
     * @param Client $clickhouse
     * @param ClickhouseConfig $clickhouseConfig
     */
    public function __construct(EntityManager $entityManager, Client $clickhouse, ClickhouseConfig $clickhouseConfig)
    {
        $this->entityManager = $entityManager;
        $this->clickhouse = $clickhouse;
        $this->clickhouseConfig = $clickhouseConfig;
    }

    /**
     * @param AnalyticsSite $analyticsSite
     * @param string $event
     * @param array $properties
     * @param null $date
     * @param null $sessionId
     * @return array
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function track(AnalyticsSite $analyticsSite, string $event, array $properties, $date = null, $sessionId = null)
    {

        $eventId = AnalyticsEvents::guidv4();

        if (!$date) {
            $date = new \DateTime();
        }

        $eventData = [
            'event_id' => $eventId,
            'site_id' => $analyticsSite->getId(),
            'name' => $event,
            'session_id' => $sessionId,
            'created_at' => $date
        ];

        $this->clickhouse->insertAssocBulk($this->clickhouseConfig->getTableName(AnalyticsEvents::class), $eventData);

        $eventData['properties'] = [];

        foreach ($properties as $key => $item) {
            $propertyData = [
                'property_id' => AnalyticsEventProperties::guidv4(),
                'event_id' => $eventId,
                'name' => $key,
                'value' => $item,
                'created_at' => $date
            ];

            $this->clickhouse->insertAssocBulk($this->clickhouseConfig->getTableName(AnalyticsEventProperties::class), $propertyData);

            $eventData['properties'][] = $propertyData;
        }

        return $eventData;
    }
}

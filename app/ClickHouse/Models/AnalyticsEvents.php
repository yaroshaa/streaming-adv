<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;
use DateTime;

/**
 * Class AnalyticsEvents
 * @package App\ClickHouse\Models
 */
class AnalyticsEvents extends Base
{
    const EVENT_PAGEVIEW = 'pageview';
    const EVENT_CLICK = 'click';

    /**
     * @var int
     */
    private int $eventId;

    /**
     * @var int
     */
    private int $siteId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     */
    public function setSiteId(int $siteId): void
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;
use DateTime;

class AnalyticsEventProperties extends Base
{
    /**
     * @var int
     */
    private int $propertyId;

    /**
     * @var AnalyticsEvents
     */
    private AnalyticsEvents $event;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $value;

    /**
     * @return int
     */
    public function getPropertyId(): int
    {
        return $this->propertyId;
    }

    /**
     * @param int $propertyId
     */
    public function setPropertyId(int $propertyId): void
    {
        $this->propertyId = $propertyId;
    }

    /**
     * @return AnalyticsEvents
     */
    public function getEvent(): AnalyticsEvents
    {
        return $this->event;
    }

    /**
     * @param AnalyticsEvents $event
     */
    public function setEvent(AnalyticsEvents $event): void
    {
        $this->event = $event;
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
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }


}

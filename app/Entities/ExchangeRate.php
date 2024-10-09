<?php

namespace App\Entities;

use DateTime;

/**
 * ExchangeRates
 */
class ExchangeRate
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @var Currency
     */
    private Currency $from;

    /**
     * @var Currency
     */
    private Currency $to;

    /**
     * @var float
     */
    private float $rate;

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set createdAt.
     *
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt.
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set from.
     *
     * @param Currency $from
     */
    public function setFrom(Currency $from): void
    {
        $this->from = $from;
    }

    /**
     * Get from.
     *
     * @return Currency
     */
    public function getFrom(): Currency
    {
        return $this->from;
    }

    /**
     * Set to.
     *
     * @param Currency $to
     */
    public function setTo(Currency $to): void
    {
        $this->to = $to;
    }

    /**
     * Get to.
     *
     * @return Currency
     */
    public function getTo(): Currency
    {
        return $this->to;
    }
}

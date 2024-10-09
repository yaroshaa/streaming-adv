<?php


namespace App\ClickHouse\Models;


use \DateTime;

/**
 * @OA\Schema(
 *     schema="marketingExpense",
 *     title="Marketing Expense",
 *     description="Marketing Expense"
 * )
 *
 * CartAction
 */
class MarketingExpense
{
    /**
     * @var int
     */
    private int $marketingChannelId;

    /**
     * @var int
     */
    private int $marketId;

    /**
     * @var int
     */
    private int $currencyId;

    /**
     * @var float
     */
    private float $value;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @return int
     */
    public function getMarketingChannelId(): int
    {
        return $this->marketingChannelId;
    }

    /**
     * @param int $marketingChannelId
     */
    public function setMarketingChannelId(int $marketingChannelId): void
    {
        $this->marketingChannelId = $marketingChannelId;
    }

    /**
     * @return int
     */
    public function getMarketId(): int
    {
        return $this->marketId;
    }

    /**
     * @param int $marketId
     */
    public function setMarketId(int $marketId): void
    {
        $this->marketId = $marketId;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     */
    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value): void
    {
        $this->value = $value;
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

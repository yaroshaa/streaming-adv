<?php


namespace App\ClickHouse\Models;


use \DateTime;

/**
 * @OA\Schema(
 *     schema="userHistoryStatistic",
 *     title="User History Statistic",
 *     description="ClickHouse UserHistoryStatistic entity"
 * )
 *
 * UserHistoryStatistic
 */
class UserHistoryStatistic
{
    /**
     * @var int
     */
    private int $marketId;

    /**
     * @var string
     */
    private string $ip;

    /**
     * @var int
     */
    private int $status;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @return string
     */
    public function getMarketId(): string
    {
        return $this->marketId;
    }

    /**
     * @param string $marketId
     */
    public function setMarketId(string $marketId): void
    {
        $this->marketId = $marketId;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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

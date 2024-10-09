<?php


namespace App\ClickHouse\Models;


use DateTime;

class OrderStatusHistory
{
    /**
     * @var string
     */
    private string $orderId;
    /**
     * @var int
     */
    private int $statusBefore;
    /**
     * @var int
     */
    private int $statusAfter;
    /**
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getStatusBefore(): int
    {
        return $this->statusBefore;
    }

    /**
     * @param int $statusBefore
     */
    public function setStatusBefore(int $statusBefore): void
    {
        $this->statusBefore = $statusBefore;
    }

    /**
     * @return int
     */
    public function getStatusAfter(): int
    {
        return $this->statusAfter;
    }

    /**
     * @param int $statusAfter
     */
    public function setStatusAfter(int $statusAfter): void
    {
        $this->statusAfter = $statusAfter;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}

<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;
use DateTime;

class FifteenMinTotals implements ClickHouseModel
{
    /**
     * @var float
     */
    private float $profit;

    /**
     * @var int
     */
    private int $ordersCnt;

    /**
     * @var float
     */
    private float $total;

    /**
     * @var DateTime|null
     */
    private ?DateTime $date;

    /**
     * @return float
     */
    public function getProfit(): float
    {
        return $this->profit;
    }

    /**
     * @param float $profit
     */
    public function setProfit(float $profit): void
    {
        $this->profit = $profit;
    }

    /**
     * @return int
     */
    public function getOrdersCnt(): int
    {
        return $this->ordersCnt;
    }

    /**
     * @param int $ordersCnt
     */
    public function setOrdersCnt(int $ordersCnt): void
    {
        $this->ordersCnt = $ordersCnt;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     */
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }
}

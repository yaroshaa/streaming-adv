<?php


namespace App\ClickHouse\Models;


/**
 * Class WarehouseStatistic
 * @package App\ClickHouse\Models
 */
class WarehouseStatistic
{
    /**
     * @var int
     */
    private int $warehouseId;

    /**
     * @var int
     */
    private int $inPacking;

    /**
     * @var int
     */
    private int $open;

    /**
     * @var float
     */
    private float $awaitingStock;

    /**
     * @return int
     */
    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }

    /**
     * @param int $warehouseId
     */
    public function setWarehouseId(int $warehouseId): void
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return int
     */
    public function getInPacking(): int
    {
        return $this->inPacking;
    }

    /**
     * @param int $inPacking
     */
    public function setInPacking(int $inPacking): void
    {
        $this->inPacking = $inPacking;
    }

    /**
     * @return int
     */
    public function getOpen(): int
    {
        return $this->open;
    }

    /**
     * @param int $open
     */
    public function setOpen(int $open): void
    {
        $this->open = $open;
    }

    /**
     * @return float
     */
    public function getAwaitingStock(): float
    {
        return $this->awaitingStock;
    }

    /**
     * @param float $awaitingStock
     */
    public function setAwaitingStock(float $awaitingStock): void
    {
        $this->awaitingStock = $awaitingStock;
    }
}

<?php

namespace App\Entities;

use DateTime;
/**
 *
 * @OA\Schema(
 *      schema="newOrderData",
 *      title="new Order Data",
 *      allOf={
 *              @OA\Schema(ref="#/components/schemas/Order"),
 *              @OA\Schema(ref="#/components/schemas/Address"),
 *              @OA\Schema(
 *                  @OA\Property(property="currency", type="object", ref="#/components/schemas/Currency"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="customer", type="object", ref="#/components/schemas/Customer"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="market", type="object", ref="#/components/schemas/Market"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="status", type="object", ref="#/components/schemas/OrderStatus"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/OrderProduct"))
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="warehouse", type="object", ref="#/components/schemas/Warehouse")
 *              ),
 *     }
 * )
 *
 * @OA\Schema(
 *     title="Order",
 *     description="Order model"
 * )
 *
 * Orders
 */
class Order
{
    /**
     * The unique identifier of a order.
     *
     * @var integer
     * @OA\Property(format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The unique identifier of a order in remote table.
     *
     * @var string
     * @OA\Property(property="order_id", example="9303")
     */
    private string $remoteId;

    /**
     * Packing cost of order
     * @OA\Property(property="packing_cost", example="123.33")
     * @var float
     */
    private float $packingCost;

    /**
     * @var Warehouse
     */
    private Warehouse $warehouse;

    /**
     * Initial creation timestamp
     *
     * @var DateTime
     * @OA\Property(property="created_at", type="string", readOnly="true", format="date-time", description="Initial creation timestamp"),
     */
    private DateTime $createdAt;

    /**
     * Last update timestamp
     *
     * @var DateTime
     * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
     */
    private DateTime $updatedAt;

    /**
     * @var Address
     */
    private Address $address;

    /**
     * @var Currency
     */
    private Currency $currency;

    /**
     * @var Customer
     */
    private Customer $customer;

    /**
     * @var Market
     */
    private Market $market;

    /**
     * @var OrderStatus
     */
    private OrderStatus $orderStatus;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    /**
     * @param string $remoteId
     * @return void
     */
    public function setRemoteId(string $remoteId): void
    {
        $this->remoteId = $remoteId;
    }

    /**
     * @return float
     */
    public function getPackingCost(): float
    {
        return $this->packingCost;
    }

    /**
     * @param float $packingCost
     */
    public function setPackingCost(float $packingCost): void
    {
        $this->packingCost = $packingCost;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    /**
     * Set createdAt.
     *
     * @param DateTime $createdAt
     *
     * @return void
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
     *
     * @return void
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
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    /**
     * Set currency.
     *
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * Get currency.
     *
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Set customer.
     *
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * Get customer.
     *
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * Set market.
     *
     * @param Market $market
     */
    public function setMarket(Market $market): void
    {
        $this->market = $market;
    }

    /**
     * Get market.
     *
     * @return Market
     */
    public function getMarket(): Market
    {
        return $this->market;
    }

    /**
     * Set orderStatus.
     *
     * @param OrderStatus $orderStatus
     */
    public function setOrderStatus(OrderStatus $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * Get orderStatus.
     *
     * @return OrderStatus
     */
    public function getOrderStatus(): OrderStatus
    {
        return $this->orderStatus;
    }
}

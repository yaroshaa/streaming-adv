<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;
use DateTime;

class Order implements ClickHouseModel
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @var OrderStatus
     */
    private OrderStatus $status;

    /**
     * @var Market
     */
    private Market $market;

    /**
     * @var Customer
     */
    private Customer $customer;
    /**
     * @var int
     */
    private int $currency;

    /**
     * @var Address
     */
    private Address $address;

    /**
     * @var OrderProduct[]
     */
    private array $products = [];

    /**
     * @var int|null
     */
    private ?int $sign = null;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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

    /**
     * @return OrderProduct[]
     */
    public function getOrderProducts(): array
    {
        return $this->products;
    }

    /**
     * @param OrderProduct $product
     */
    public function addOrderProduct(OrderProduct $product): void
    {
        $this->products[] = $product;
    }

    /**
     * @return int
     */
    public function getCurrency(): int
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     */
    public function setCurrency(int $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @param OrderStatus $status
     */
    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Market
     */
    public function getMarket(): Market
    {
        return $this->market;
    }

    /**
     * @param Market $market
     */
    public function setMarket(Market $market): void
    {
        $this->market = $market;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
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
     * @return int|null
     */
    public function getSign(): ?int
    {
        return $this->sign;
    }

    /**
     * @param int|null $sign
     */
    public function setSign(?int $sign): void
    {
        $this->sign = $sign;
    }
}

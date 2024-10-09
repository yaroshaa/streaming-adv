<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;

/**
 * Class OrderProduct
 * @package App\ClickHouse\Models
 */
class OrderProduct implements ClickHouseModel
{
    /**
     * The unique identifier of order product.
     *
     * @var int
     */
    private int $id;

    /**
     * The product price.
     *
     * @var float
     */
    private float $price;

    /**
     * The product profit.
     *
     * @var float
     */
    private float $profit;

    /**
     * The quantity of products.
     *
     * @var int
     */
    private int $qty;

    /**
     * The name of product.
     *
     * @var string
     */
    private string $name;

    /**
     * The link for product.
     *
     * @var string
     */
    private string $link;

    /**
     * The link of image for product.
     *
     * @var string
     */
    private string $imageLink;

    /**
     * @var string
     */
    private string $marketName;

    /**
     * @var string
     */
    private string $marketIconLink;

    /**
     * The product discount.
     *
     * @var float
     */
    private float $discount;

    /**
     * The product weight.
     *
     * @var float
     */
    private float $weight;

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
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     */
    public function setQty(int $qty): void
    {
        $this->qty = $qty;
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
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    /**
     * @param string $imageLink
     */
    public function setImageLink(string $imageLink): void
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string
     */
    public function getMarketName(): string
    {
        return $this->marketName;
    }

    /**
     * @param string $marketName
     */
    public function setMarketName(string $marketName): void
    {
        $this->marketName = $marketName;
    }

    /**
     * @return string
     */
    public function getMarketIconLink(): string
    {
        return $this->marketIconLink;
    }

    /**
     * @param string $marketIconLink
     */
    public function setMarketIconLink(string $marketIconLink): void
    {
        $this->marketIconLink = $marketIconLink;
    }

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
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }
}

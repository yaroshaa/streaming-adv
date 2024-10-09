<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;
use DateTime;

/**
 * @OA\Schema(
 *     schema="feedback",
 *     title="Feedback",
 *     description="ClickHouse feedback entity"
 * )
 *
 * Feedback
 */
class Feedback implements ClickHouseModel
{
    /**
     * @OA\Property(property="unique_id", format="string", readOnly="true", example="6799289c701cefdb28800f5f547d94018edea152d2b248b5db4edbfde4516e0fe2fee2323d041fa87e4fbbbe42a10487bc0b533fa9f4027fac615581dd3fde12")
     */
    private string $uniqueId;

    /**
     * @OA\Property(property="name", format="string", example="First Last")
     */
    private string $name;

    /**
     * @OA\Property(property="message", format="string", example="Example comment or feedback")
     */
    private string $message;

    /**
     * @OA\Property(property="created_at", format="string", example="2021-03-19T09:45:22.145Z")
     */
    private DateTime $createdAt;

    /**
     * @OA\Property(property="market_id", type="integer", format="number", example="2")
     */
    private Market $market;

    /**
     * @OA\Property(property="source_id",  type="integer", format="number", example="2")
     */
    private Source $source;

    /**
     * @OA\Property(property="url", format="string", example="http://localhost/foo/bar")
     */
    private string $url;

    /**
     * Feedback constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     * @return void
     */
    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
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
     * @return Source
     */
    public function getSource(): Source
    {
        return $this->source;
    }

    /**
     * @param Source $source
     */
    public function setSource(Source $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}

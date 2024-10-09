<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     title="MarketingChannel",
 *     description="MarketingChannel model"
 * )
 *
 * MarketingChannel
 */
class MarketingChannel
{
    /**
     * The unique identifier of a marketing channel.
     *
     * @var int
     * @OA\Property(property="id", format="int64", readOnly="true", example="1")
     */
    private int $id;

    /**
     * The name of marketing channel.
     *
     * @var string
     * @OA\Property(example="Facebook")
     */
    private string $name;

    /**
     * The name of marketing channel.
     *
     * @var string
     * @OA\Property(example="/images/marketing_channel/facebook.svg")
     */
    private ?string $iconLink;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getIconLink(): ?string
    {
        return $this->iconLink;
    }

    /**
     * @param string $iconLink
     */
    public function setIconLink(string $iconLink): void
    {
        $this->iconLink = $iconLink;
    }
}

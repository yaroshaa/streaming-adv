<?php

namespace App\Entities;

/**
 *  @OA\Schema(
 *     title="Source",
 *     description="Source model"
 * )
 * Markets
 */
class Source
{
    /**
     * The unique identifier of a market.
     *
     * @var int
     * @OA\Property(property="id", format="int64", readOnly="true", example="1")
     */
    private int $id;

    /**
     * The market name.
     *
     * @var string
     * @OA\Property(example="helpscout")
     */
    private string $name;

    /**
     * The unique identifier of a market in remote database.
     *
     * @var int
     * @OA\Property(property="remote_id", format="int64", example="4")
     */
    private int $remoteId;

    /**
     * The link for market icon.
     *
     * @var string
     * @OA\Property(format="url", example="/images/sources/helpscout.jpeg")
     */
    private string $iconLink;

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
     * @return int
     */
    public function getRemoteId(): int
    {
        return $this->remoteId;
    }

    /**
     * @param int $remoteId
     */
    public function setRemoteId(int $remoteId): void
    {
        $this->remoteId = $remoteId;
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
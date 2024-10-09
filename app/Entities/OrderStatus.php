<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     title="OrderStatus",
 *     description="Order statuses"
 * )
 *
 * OrderStatuses
 */
class OrderStatus
{
    /**
     * The unique identifier of a order status.
     *
     * @var int
     * @OA\Property(property="base_id", format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The name of a status.
     *
     * @var string
     * @OA\Property(example="new")
     */
    private string $name;

    /**
     * The unique identifier of a order status in remote table.
     *
     * @var int
     * @OA\Property(property="remote_id", format="int64", example="4")
     */
    private int $remoteId;

    /**
     * The color of a market.
     *
     * @var string|null
     * @OA\Property(example="green")
     */
    private ?string $color;

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
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}

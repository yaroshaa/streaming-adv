<?php

namespace App\Entities;


use DateTime;

/**
 * @OA\Schema(
 *     title="Customer",
 *     description="Customer model"
 * )
 *
 * Customer
 */
class Customer
{
    /**
     * The unique identifier of a customer.
     *
     * @var int
     * @OA\Property(property="base_id", format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The name of a customer.
     *
     * @var string
     * @OA\Property(example="Liyah Swift")
     */
    private string $name;

    /**
     * The unique identifier of a customer in remote database.
     *
     * @var int
     * @OA\Property(property="remote_id", format="int64", example="6554")
     */
    private int $remoteId;

    /**
     * Initial creation timestamp
     *
     * @var DateTime|null
     * @OA\Property(property="created_at", type="string", readOnly="true", format="date-time", description="Initial creation timestamp"),
     */
    private ?DateTime $createdAt;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
     * @return ?DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }
}

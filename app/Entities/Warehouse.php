<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     schema="Warehouse",
 *     title="Warehouse",
 *     description="Warehouse model"
 * )
 *
 * Warehouse
 */
class Warehouse
{
    /**
     * The unique identifier of a warehouse.
     *
     * @var int
     * @OA\Property(property="id", format="int64", readOnly="true", example="1")
     */
    private int $id;

    /**
     * The name of warehouse.
     *
     * @var string
     * @OA\Property(example="Vestby")
     */
    private string $name;

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
}

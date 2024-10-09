<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     title="Currency",
 *     description="Currency model"
 * )
 *
 * Currency
 */
class Currency
{
    /**
     * The unique identifier of a currency.
     *
     * @var int
     * @OA\Property(format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The name of a currency.
     *
     * @var string
     * @OA\Property(example="USD")
     */
    private string $name;

    /**
     * The code of a currency.
     *
     * @var string
     * @OA\Property(example="USD")
     */
    private string $code;


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
     * Set code.
     *
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'name' => $this->name,
           'code' => $this->code,
        ];
    }
}

<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     title="AnalyticsSite",
 *     description="AnalyticsSite model"
 * )
 *
 * AnalyticsSite
 */
class AnalyticsSite
{
    /**
     * The unique identifier of a site.
     *
     * @var int
     * @OA\Property(property="base_id", format="int64", readOnly="true", example="1")
     */
    private int $id;

    /**
     * The name of site.
     *
     * @var string
     * @OA\Property(example="example.com")
     */
    private string $name;

    /**
     * The key of site.
     *
     * @var string
     * @OA\Property(example="111-222-333-444")
     */
    private string $key;

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
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}

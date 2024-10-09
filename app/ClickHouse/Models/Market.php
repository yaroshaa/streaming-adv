<?php


namespace App\ClickHouse\Models;


use App\ClickHouse\ClickHouseModel;

class Market implements ClickHouseModel
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string|null
     */
    private ?string $name;
    /**
     * @var string|null
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
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getIconLink(): ?string
    {
        return $this->iconLink;
    }

    /**
     * @param string|null $iconLink
     */
    public function setIconLink(?string $iconLink): void
    {
        $this->iconLink = $iconLink;
    }
}
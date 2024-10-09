<?php


namespace App\Entities;

/**
 * @OA\Schema(
 *     title="Address",
 *     description="Address model"
 * )
 *
 * Address
 */
class Address
{
    /**
     * The unique identifier of a address.
     *
     * @var int
     * @OA\Property(format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The full address.
     *
     * @var string
     * @OA\Property(example="Karlshof, 72351 Geislingen, Германия")
     */
    private string $address;

    /**
     * The latitude coordinate.
     *
     * @var float
     * @OA\Property(readOnly="true")
     */
    private float $lat;

    /**
     * The longitude coordinate.
     *
     * @var float
     * @OA\Property(readOnly="true")
     */
    private float $lng;

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
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat(float $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * @param float $lng
     */
    public function setLng(float $lng): void
    {
        $this->lng = $lng;
    }

}

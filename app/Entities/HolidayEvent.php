<?php

namespace App\Entities;

/**
 * @OA\Schema(
 *     title="HolidayEvent",
 *     description="HolidayEvent model"
 * )
 *
 * Holiday Event
 */
class HolidayEvent
{
    /**
     * The unique identifier of a holiday event.
     *
     * @var int
     * @OA\Property(property="id", format="int64", readOnly="true")
     */
    private int $id;

    /**
     * The holiday event title.
     *
     * @var string
     * @OA\Property(example="Green friday")
     */
    private string $title;

    /**
     * Initial creation timestamp
     *
     * @var \DateTime|null
     * @OA\Property(property="date", type="string", readOnly="true", format="date-time", description="Date of event"),
     */
    private ?\DateTime $date;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }
}

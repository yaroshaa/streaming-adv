<?php


namespace App\ClickHouse;


use DateTime;

class Converter
{
    const TO_DATE = 'to_date';
    const TO_NULLABLE_DATE = 'to_nullable_date';

    /**
     * @var array
     */
    private array $convertors = [];

    /**
     * Converter constructor.
     */
    public function __construct()
    {
        $this->convertors[self::TO_DATE] = fn($val) => new DateTime($val);
        $this->convertors[self::TO_NULLABLE_DATE] = fn($val) => null === $val ? null : new DateTime($val);
    }

    /**
     * @param $value
     * @param string $convertor
     * @return false|mixed
     * @throws ClickHouseException
     */
    public function convert($value, string $convertor)
    {
        if (!array_key_exists($convertor, $this->convertors)) {
            throw new ClickHouseException(sprintf('Convertor %s does not exists', $convertor));
        }

        return call_user_func_array($this->convertors[$convertor], [$value]);
    }
}
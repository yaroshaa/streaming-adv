<?php


namespace App\ClickHouse;


/**
 * Interface QueryInterface
 * @deprecated
 * @package App\ClickHouse
 */
interface QueryInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function getQuery(array $data = []): string;
}

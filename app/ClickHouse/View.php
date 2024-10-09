<?php


namespace App\ClickHouse;


interface View
{
    /**
     * @return string
     */
    public function getQuery(): string;

    /**
     * @return string
     */
    public static function getName(): string;
}
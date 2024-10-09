<?php


namespace App\ClickHouse\Queries;


use App\ClickHouse\ClickhouseConfig;

/**
 * Class BaseQuery
 * @deprecated Need rework on for DB Views Context
 * @package App\ClickHouse\Queries
 */
class BaseQuery
{
    /**
     * @var ClickhouseConfig
     */
    protected ClickhouseConfig $clickhouseConfig;

    /**
     * BaseQuery constructor.
     * @param ClickhouseConfig $clickhouseConfig
     */
    public function __construct(ClickhouseConfig $clickhouseConfig)
    {
        $this->clickhouseConfig = $clickhouseConfig;
    }
}

<?php

namespace App\ClickHouse\QuickQueries;

use App\ClickHouse\ClickhouseConfig;

abstract class BaseQuickQuery
{
    protected ClickhouseConfig $clickhouseConfig;

    /**
     * Return final SQL for execute
     * @return string
     */
    abstract function __toString(): string;

    public function setClickhouseConfig(ClickhouseConfig $clickhouseConfig)
    {
        $this->clickhouseConfig = $clickhouseConfig;
    }
}

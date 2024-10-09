<?php

namespace App\Vendor\ClickHouseDB;

use ClickHouseDB\Client as BaseClient;
use ClickHouseDB\Statement;
use Illuminate\Support\Str;

class Client extends BaseClient
{
    public function insert(string $table, array $values, array $columns = []): Statement
    {
        $statement = parent::insert($table, $values, $columns);
        event('ClickHouse.Insert.' . Str::ucfirst(Str::camel($table)));
        return $statement;
    }
}

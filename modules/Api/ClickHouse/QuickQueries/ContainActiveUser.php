<?php

namespace Modules\Api\ClickHouse\QuickQueries;

use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;

class ContainActiveUser extends BaseQuickQuery
{
    public function __construct($ip, $status)
    {

    }

    function __toString(): string
    {
        $dbName = QueryHelper::getDbName();

    }
}

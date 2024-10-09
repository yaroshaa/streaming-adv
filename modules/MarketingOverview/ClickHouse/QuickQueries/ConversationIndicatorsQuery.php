<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Exception;

class ConversationIndicatorsQuery extends BaseQuickQuery
{
    private string $granularity;

    public function __construct(string $granularity)
    {
        $this->granularity = $granularity;
    }

    /**
     * @throws Exception
     */
    function __toString(): string
    {
        $granularity = QueryHelper::getDateGranularity($this->granularity, QueryHelper::FIELD_DATE_CREATED_AT);

        return <<<SQL
            SELECT {$granularity} AS date,
                                count(1) as value,
                                'active_users' as entity
                                FROM active_users
                                WHERE status = 1
                                GROUP BY date
                                ORDER BY date
            UNION ALL SELECT {$granularity} AS date,
                                count(1) as value,
                                'cart_actions' as entity
                                FROM cart_actions
                                WHERE status = 1
                                GROUP BY date
                                ORDER BY date
            UNION ALL SELECT {$granularity} AS date,
                                count(1) as value,
                                'orders' as entity
                                FROM orders
                                GROUP BY date
                                ORDER BY date
SQL;
    }
}

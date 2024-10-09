<?php

namespace Modules\CompanyOverview\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class TotalsQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private int $currencyId;

    public function __construct(Carbon $from, Carbon $to, int $currencyId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->currencyId = $currencyId;
    }

    /**
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $dbname = QueryHelper::getDbName();

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to),
        ]);

        $previousWhere = QueryHelper::where([
            QueryHelper::getPeriodConditionForPreviousInterval($this->from, $this->to),
        ]);

        $sqlTemplate = <<< SQL
            SELECT sum(profit) as profit,
                   sum(total)  as total,
                   (
                       SELECT count(DISTINCT order_id)
                       FROM {$tableName}
                        :where
                   )           as orders_count,
                   ':interval' as interval
            FROM (
                  SELECT product_profit * rate                                        as profit,
                         product_price * rate                                         as total,
                         dictGet('{$dbname}.exchange_rates', 'rate',
                                 tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  FROM {$tableName}
                  :where
                     )
SQL;

        $current = str_replace([':interval', ':where'], ['current', $where], $sqlTemplate);
        $previous = str_replace([':interval', ':where'], ['previous', $previousWhere], $sqlTemplate);

        return <<< SQL
            {$current}
            UNION ALL
            {$previous}
SQL;
    }
}

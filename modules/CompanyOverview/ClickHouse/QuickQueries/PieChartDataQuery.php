<?php

namespace Modules\CompanyOverview\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class PieChartDataQuery extends BaseQuickQuery
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

        return <<<SQL
            SELECT name, sum(value) as value
            FROM (
                  WITH dictGet('{$dbname}.exchange_rates', 'rate',
                               tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  SELECT dictGet('{$dbname}.markets', 'name', market_id) as name,
                         product_price * rate                            as value
                  FROM {$tableName}
                  {$where}
                     )
            GROUP BY name
SQL;
    }
}

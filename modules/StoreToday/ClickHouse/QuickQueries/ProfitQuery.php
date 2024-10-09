<?php

namespace Modules\StoreToday\ClickHouse\QuickQueries;

use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class ProfitQuery extends BaseQuickQuery
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

    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $dbname = QueryHelper::getDbName();

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to)
        ]);

        return <<<SQL
            WITH dictGet('{$dbname}.customers', 'created_at', customer_id)                                 as customer_created_at
            SELECT sum(profit)                                                                             as total_profit,
                   sum(profit + discount)                                                                  as revenue
            FROM (
                      WITH dictGet('{$dbname}.exchange_rates', 'rate', tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                      SELECT *,
                             dictGet('{$dbname}.orders', 'order_status_id', tuple(order_id)) as status_id,
                             product_price * rate  as total,
                             product_profit * rate as profit,
                             product_discount * rate as discount
                      FROM {$tableName}
                      {$where}
                  )
SQL;
    }
}

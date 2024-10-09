<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;
use Exception;

class ExpenseOverPeriodQuery extends BaseQuickQuery
{
    private ?Carbon $from;
    private ?Carbon $to;
    private string $granularity;
    private int $currencyId;

    public function __construct(?Carbon $from, ?Carbon $to, string $granularity, int $currencyId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->granularity = $granularity;
        $this->currencyId = $currencyId;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(MarketingExpense::class);
        $dbName = QueryHelper::getDbName();

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT)
        ]);

        $granularity = QueryHelper::getDateGranularity($this->granularity, QueryHelper::FIELD_DATE_CREATED_AT);

        return <<<SQL
            SELECT {$granularity} as date,
                   round(sum(expense), 2) as marketing_expense
            FROM (
                  WITH dictGet('{$dbName}.exchange_rates', 'rate', tuple(currency_id, toUInt64({$this->currencyId}), toDate(created_at))) as rate
                  SELECT *,
                         value * rate  as expense
                  FROM {$tableName}
                  {$where}
            )
            GROUP BY date
            ORDER BY date DESC
SQL;
    }
}

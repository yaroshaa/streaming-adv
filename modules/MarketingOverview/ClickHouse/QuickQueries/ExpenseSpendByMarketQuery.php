<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

class ExpenseSpendByMarketQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private string $granularity;
    private int $currencyId;

    public function __construct(Carbon $from, Carbon $to, string $granularity, int $currencyId)
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
        $dbName = QH::getDbName();
        $marketingExpenseTableName = $this->clickhouseConfig->getTableName(MarketingExpense::class);
        $granularity = QH::getDateGranularity($this->granularity, QH::FIELD_DATE_CREATED_AT);

        $where = QH::where([
            QH::getPeriodCondition($this->from, $this->to, QH::FIELD_DATE_CREATED_AT, QH::FORMAT_DATETIME)
        ]);

        return <<<SQL
                    WITH dictGet('{$dbName}.marketing_channels', 'name', marketing_chanel_id) AS marketing_chanel_name
                    SELECT {$granularity} AS date,
                    market_id,
                    any(t2.name) as market_name,
                    marketing_chanel_id,
                    marketing_chanel_name,
                    sum(spend) as spend
                    FROM (
                        WITH dictGet('{$dbName}.exchange_rates', 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(created_at))) AS rate
                        SELECT *, value * rate as spend
                        FROM {$marketingExpenseTableName}
                        $where
                    ) t1
                    JOIN (
                        SELECT remote_id, name
                        FROM markets
                    ) t2 ON t1.market_id=t2.remote_id
                    GROUP BY date, market_id, marketing_chanel_id
SQL;
    }
}

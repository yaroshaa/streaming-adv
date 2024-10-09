<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

class ExpenseSpendQuery extends BaseQuickQuery
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
WITH dictGet('{$dbName}.marketing_channels', 'name', marketing_chanel_id) AS marketing_chanel_name,
     dictGet('{$dbName}.marketing_channels', 'icon_link', marketing_chanel_id) AS marketing_chanel_icon_link
                    SELECT {$granularity} AS date,
                    marketing_chanel_id,
                    marketing_chanel_name,
                    marketing_chanel_icon_link,
                    sum(spend) as spend
                    FROM (
                        WITH dictGet('{$dbName}.exchange_rates', 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(created_at))) AS rate
                        SELECT *, value * rate as spend
                        FROM {$marketingExpenseTableName}
                        $where
                    )
                    GROUP BY marketing_chanel_id, date
                    ORDER BY date
SQL;
    }
}

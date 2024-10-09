<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;
use Exception;

class TotalQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private int $currencyId;
    private string $granularity;

    public function __construct(Carbon $from, Carbon $to, int $currencyId, string $granularity)
    {
        $this->from = $from;
        $this->to = $to;
        $this->currencyId = $currencyId;
        $this->granularity = $granularity;
    }

    /**
     * @throws ClickHouseException
     * @throws Exception
     */
    function __toString(): string
    {
        $orderProductTableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $marketingExpenseTableName = $this->clickhouseConfig->getTableName(MarketingExpense::class);
        $granularity = QueryHelper::getDateGranularity($this->granularity);

        $dbName = QueryHelper::getDbName();


        $andWhereOrderProduct = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to),
        ]);

        $andWhereMarketingExpense = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT, QueryHelper::FORMAT_DATETIME),
        ]);

        return <<<SQL
SELECT
                    {$granularity} AS date,
                    round(sum(revenue), 2) AS revenue,
                    contribution_margin - marketing_expense as cmam,
                    (revenue - marketing_expense) / revenue as contribution_margin_ratio,
                    1-((revenue - marketing_expense) / revenue) as spend_ratio,
                    sum(profit) AS contribution_margin,
                    sum(t2.marketing_expense) as marketing_expense
                FROM
                (
                    WITH dictGet('{$dbName}.exchange_rates', 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) AS rate
                    SELECT
                        *,
                        product_price * rate AS revenue,
                        product_profit * rate AS profit
                    FROM {$orderProductTableName}
                    {$andWhereOrderProduct}
                ) as t1
                LEFT JOIN (
                    WITH dictGet('{$dbName}.exchange_rates', 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(created_at))) AS rate
                    SELECT *, value * rate as marketing_expense
                    FROM {$marketingExpenseTableName}
                    {$andWhereMarketingExpense}
                ) t2 on t1.market_id=t2.market_id
                GROUP BY date
                ORDER BY date
SQL;
    }
}

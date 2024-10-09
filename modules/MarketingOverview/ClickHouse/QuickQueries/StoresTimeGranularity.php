<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;
use Exception;

class StoresTimeGranularity extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private string $granularity;

    public function __construct(Carbon $from, Carbon $to, string $granularity)
    {
        $this->from = $from;
        $this->to = $to;
        $this->granularity = $granularity;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    function __toString(): string
    {
        $orderProductTableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $marketingExpenseTableName = $this->clickhouseConfig->getTableName(MarketingExpense::class);

        $granularityByCreated = QueryHelper::getDateGranularity($this->granularity, QueryHelper::FIELD_DATE_CREATED_AT);
        $granularityByUpdated = QueryHelper::getDateGranularity($this->granularity);
        $timeAlias = QueryHelper::getDateGranularityAlias($this->granularity);

        $andWhereOrderProduct = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to)
        ]);

        $andWhereMarketingExpense = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT, QueryHelper::FORMAT_DATETIME)
        ]);

        return <<<SQL
        SELECT
                t1.market_id AS market_id,
                t1.date AS date,
                t1.{$timeAlias} AS {$timeAlias},
                round(sum(revenue), 2) AS revenue,
                sum(count_orders) as cnt_orders,
                sum(profit) AS contribution_margin,
                sum(t2.marketing_expense) AS marketing_expense,
                contribution_margin - marketing_expense AS cmam,
                sum(packing_cost) AS packing_cost,
                packing_cost + marketing_expense AS spend,
                any(t3.active_users) AS active_users,
                any(t4.add_to_cart) AS add_to_cart,
                if(active_users > 0, (sum(count_orders) / active_users), 0) AS conversion_rate,
                if(revenue > 0, (1 - ((revenue - marketing_expense) / revenue)), 0) AS spend_ratio,
                if(revenue > 0, ((revenue - marketing_expense) / revenue), 0) AS contribution_margin_ratio
            FROM
            (

                SELECT market_id,
                       updated_date_at as date,
                       {$timeAlias},
                       sum(packing_cost) as packing_cost,
                       sum(revenue) as revenue,
                       sum(profit) as profit,
                       count(*) as count_orders
                FROM (
                    WITH dictGet(exchange_rates, 'rate', (currency_id, toUInt64(3), toDate(updated_at))) AS rate
                    SELECT
                        market_id,
                        toDate(updated_at) as updated_date_at,
                        {$granularityByUpdated} as {$timeAlias},
                        dictGet(orders, 'packing_cost', tuple(order_id)) * rate AS packing_cost,
                        product_price * rate AS revenue,
                        product_profit * rate AS profit
                    FROM {$orderProductTableName}
                    {$andWhereOrderProduct}
                )
                GROUP BY market_id, date, {$timeAlias}

            ) AS t1
            LEFT JOIN
            (

                WITH dictGet(exchange_rates, 'rate', (currency_id, toUInt64(3), toDate(created_at))) AS rate
                SELECT
                    market_id,
                    toDate(created_at) as date,
                    {$granularityByCreated} as {$timeAlias},
                    sum(value * rate) AS marketing_expense
                FROM {$marketingExpenseTableName}
                {$andWhereMarketingExpense}
                GROUP BY market_id, date, {$timeAlias}

            ) AS t2 ON t1.market_id = t2.market_id and t1.date = t2.date and t1.{$timeAlias} = t2.{$timeAlias}
            LEFT JOIN (
                SELECT
                    count(*) AS active_users,
                    market_id,
                    toDate(created_at) as date,
                    {$granularityByCreated}  as {$timeAlias}
                FROM active_users
                {$andWhereMarketingExpense}
                GROUP BY market_id, date, {$timeAlias}
            ) AS t3 ON t1.market_id = t3.market_id and t1.date = t3.date and t1.{$timeAlias} = t3.{$timeAlias}
            LEFT JOIN (
                SELECT
                    count(*) AS add_to_cart,
                    market_id,
                    toDate(created_at) as date,
                    {$granularityByCreated} as {$timeAlias}
                FROM cart_actions
                {$andWhereMarketingExpense}
                GROUP BY market_id, date, {$timeAlias}
            ) AS t4 ON t1.market_id = t4.market_id  and t1.date = t4.date and t1.{$timeAlias} = t4.{$timeAlias}
            GROUP BY t1.market_id, date, {$timeAlias}
            ORDER BY t1.market_id, t1.{$timeAlias} ASC
SQL;
    }
}

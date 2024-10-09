<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class ByStoresQuery extends BaseQuickQuery
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
     * @inheritDoc
     */
    function __toString(): string
    {
        $dbName = QueryHelper::getDbName();

        $orderProductTableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $marketingExpenseTableName = $this->clickhouseConfig->getTableName(MarketingExpense::class);

        $andWhereOrderProduct = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to)
        ]);

        $andWhereMarketingExpense = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT)
        ]);

        $andWhereMarketingExpenseWithStatus = QueryHelper::where([
            'status = 1',
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT)
        ]);
        return <<<SQL
            WITH
                dictGet('{$dbName}.markets', 'name', t1.market_id) AS market_name,
                dictGet('{$dbName}.markets', 'icon_link', t1.market_id) AS market_icon_link,
                dictGet('{$dbName}.markets', 'color', t1.market_id) AS market_color
            SELECT
                t1.market_id AS market_id,
                market_name,
                market_icon_link,
                market_color,
                t1.date AS date,
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
                   sum(packing_cost) as packing_cost,
                   sum(revenue) as revenue,
                   sum(profit) as profit,
                   count(*) as count_orders
                FROM (
                    WITH dictGet(exchange_rates, 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) AS rate
                    SELECT
                        market_id,
                        toDate(updated_at) as updated_date_at,
                        dictGet(orders, 'packing_cost', tuple(order_id)) * rate AS packing_cost,
                        product_price * rate AS revenue,
                        product_profit * rate AS profit
                    FROM {$orderProductTableName}
                    {$andWhereOrderProduct}
                )
                GROUP BY market_id, date
            ) as t1
            LEFT JOIN (
                WITH dictGet(exchange_rates, 'rate', (currency_id, toUInt64({$this->currencyId}), toDate(created_at))) AS rate
                SELECT
                    market_id,
                    toDate(created_at) as date,
                    sum(value * rate) AS marketing_expense
                FROM {$marketingExpenseTableName}
                {$andWhereMarketingExpense}
                GROUP BY market_id, date
            ) t2 on t1.market_id=t2.market_id
            LEFT JOIN (
                SELECT
                    count(*) AS active_users,
                    market_id,
                    toDate(created_at) as date
                FROM active_users
                {$andWhereMarketingExpenseWithStatus}
                GROUP BY market_id, date
            ) AS t3 ON t1.market_id = t3.market_id and t1.date=t3.date
            LEFT JOIN (
                SELECT
                    count(*) AS add_to_cart,
                    market_id,
                    toDate(created_at) as date
                FROM cart_actions
                {$andWhereMarketingExpenseWithStatus}
                GROUP BY market_id, date
            ) AS t4 ON t1.market_id = t4.market_id  and t1.date=t4.date
            GROUP BY t1.market_id, date
            ORDER BY date
SQL;
    }
}

<?php

namespace Modules\Orders\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;

class FifteenMinTotalsQuery extends BaseQuickQuery
{
    private Carbon $from;
    private int $currencyId;
    private ?string $percentile;
    private ?string $productVariantId;
    private array $statusIds;
    private array $marketIds;
    private array $weight;

    public function __construct(
        Carbon $from,
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    )
    {
        $this->from = $from;
        $this->currencyId = $currencyId;
        $this->percentile = $percentile;
        $this->productVariantId = $productVariantId;
        $this->statusIds = $statusIds;
        $this->marketIds = $marketIds;
        $this->weight = $weight;
    }

    /**
     * @inheritDoc
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $dbName = QH::getDbName();

        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);

        $where = QH::where([
            QH::getAfterDate($this->from, QH::FIELD_DATE_UPDATED_AT),
            QH::condition('>=', 'updated_at', 'toStartOfDay(today())'),
            Qh::condition('=', 'product_variant_id', QH::formatValue($this->productVariantId)),
            QH::skipEmpty($this->marketIds, fn($value) => QH::in('market_id', $value)),
            QH::skipEmpty($this->statusIds, fn($value) => QH::in('status_id', $value)),
            QH::percentile($this->percentile, $this->currencyId),
            QH::weight($this->weight)
        ]);

        $previousWhere = QH::where([
            QH::getPeriodConditionForPreviousInterval($this->from, Carbon::now()),
        ]);

        $sqlTemplate = <<< SQL
         SELECT round(sum(product_profit) * 4, 2) as profit,
                   round(sum(product_price) * 4, 2)  as total,
                   count(DISTINCT order_id)          as orders_count,
                   ':interval'                       as interval
            FROM (
                  WITH dictGet('{$dbName}.exchange_rates', 'rate',
                               tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  SELECT product_profit * rate as product_profit,
                         product_price * rate  as product_price,
                         order_id,
                         dictGet('{$dbName}.orders', 'order_status_id', tuple(order_id)) as status_id
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

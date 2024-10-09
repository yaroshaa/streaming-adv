<?php

namespace Modules\Orders\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;

class HistoryTotalsQuery extends BaseQuickQuery
{
    private int $currencyId;
    private ?string $percentile;
    private ?string $productVariantId;
    private array $statusIds;
    private array $marketIds;
    private array $weight;

    public function __construct(
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    )
    {
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
            QH::condition('>=', 'updated_at', 'toStartOfDay(today())'),
            QH::condition('=', 'product_variant_id', QH::formatValue($this->productVariantId)),
            QH::skipEmpty($this->marketIds, fn($value) => QH::in('market_id', $value)),
            QH::skipEmpty($this->statusIds, fn($value) => QH::in('status_id', $value)),
            QH::percentile($this->percentile, $this->currencyId),
            QH::weight($this->weight)
        ]);

        return <<<SQL
            SELECT round(sum(product_profit), 2) as profit,
                   round(sum(product_price), 2)  as total,
                   count(DISTINCT order_id)      as orders_count,
                   toStartOfHour(updated_at)     as date
            FROM (
                  WITH dictGet('{$dbName}.exchange_rates', 'rate',
                               tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  SELECT product_profit * rate as product_profit,
                         product_price * rate  as product_price,
                         updated_at,
                         order_id,
                         dictGet('{$dbName}.orders', 'order_status_id', tuple(order_id)) as status_id
                  FROM {$tableName}
                  {$where}
                )
            GROUP BY date
SQL;
    }
}

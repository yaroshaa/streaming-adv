<?php

namespace Modules\Orders\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;

class TopSellingProductsQuery extends BaseQuickQuery
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
            SELECT product_variant_id,
                   dictGet('{$dbName}.exchange_rates', 'rate',
                         tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate,
                   dictGet('{$dbName}.product_variants', 'name', tuple(product_variant_id))       as product_name,
                   round(avg(product_weight), 2)                                           as product_weight,
                   dictGet('{$dbName}.product_variants', 'link', tuple(product_variant_id))       as product_link,
                   dictGet('{$dbName}.product_variants', 'image_link', tuple(product_variant_id)) as product_image_link,
                   dictGet('{$dbName}.markets', 'name', market_id)                         as market_name,
                   dictGet('{$dbName}.markets', 'icon_link', market_id)                    as market_icon_link,
                   sum(product_qty)                                                      as product_qty,
                   round(sum(product_price) * rate, 2)                                   as product_price,
                   round(sum(product_discount) * rate, 2)                                as product_discount,
                   round(sum(product_profit) * rate, 2)                                  as product_profit,
                   dictGet('{$dbName}.orders', 'order_status_id', tuple(order_id))              as status_id
            FROM {$tableName}
            {$where}
            GROUP BY product_variant_id, market_id, currency_id, updated_at, order_id
            ORDER BY product_qty DESC, product_profit DESC
            LIMIT 15
SQL;
    }
}

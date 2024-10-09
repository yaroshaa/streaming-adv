<?php

namespace Modules\Orders\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;

class OrderFeedQuery extends BaseQuickQuery
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

        $where = Qh::where([
            QH::getAfterDate($this->from, QH::FIELD_DATE_UPDATED_AT),
            QH::condition('>=', 'updated_at', 'toStartOfDay(today())'),
            QH::condition('=', 'product_variant_id', QH::formatValue($this->productVariantId)),
            QH::skipEmpty($this->marketIds, fn($value) => QH::in('market_id', $value)),
            QH::skipEmpty($this->statusIds, fn($value) => QH::in('status_id', $value)),
            QH::percentile($this->percentile, $this->currencyId),
            QH::weight($this->weight)
        ]);

        return <<<SQL
            SELECT order_id,
                   dictGet('{$dbName}.exchange_rates', 'rate',
                       tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate,
                   product_variant_id,
                   product_qty,
                   updated_at,
                   dictGet('{$dbName}.orders', 'order_status_id', tuple(order_id))    as status_id,
                   dictGet('{$dbName}.order_statuses', 'name', status_id)      as status_name,
                   dictGet('{$dbName}.order_statuses', 'color', status_id)     as status_color,
                   customer_id,
                   dictGet('{$dbName}.customers', 'name', customer_id)   as customer_name,
                   dictGet('{$dbName}.customers', 'created_at', customer_id)   as customer_created_at,
                   market_id,
                   currency_id,
                   dictGet('{$dbName}.orders', 'address_id', tuple(order_id))   as address_id,
                   dictGet('{$dbName}.addresses', 'address', address_id) as address,
                   dictGet('{$dbName}.addresses', 'lat', address_id)     as address_lat,
                   dictGet('{$dbName}.addresses', 'lng', address_id)     as address_lng,
                   dictGet('{$dbName}.markets', 'name', market_id)       as market_name,
                   dictGet('{$dbName}.markets', 'icon_link', market_id)  as market_icon_link,
                   round(product_weight, 2)                            as product_weight,
                   round(product_profit * rate, 2)                     as product_profit,
                   round(product_price * rate, 2)                      as product_price,
                   product_discount,
                   dictGet('{$dbName}.product_variants', 'name', tuple(product_variant_id))       as product_name,
                   dictGet('{$dbName}.product_variants', 'link', tuple(product_variant_id))       as product_link,
                   dictGet('{$dbName}.product_variants', 'image_link', tuple(product_variant_id)) as product_image_link
            FROM {$tableName}
            {$where}
            LIMIT 50
SQL;
    }
}

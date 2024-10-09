<?php

namespace App\ClickHouse\Services;

use App\ClickHouse\DateGranularityInterface;
use DateTime;
use Exception;
use Illuminate\Support\Arr;

class OrderStatQueryFilter
{
    public static $dateGranularityOptions = [
        DateGranularityInterface::DAY_GRANULARITY,
        DateGranularityInterface::WEEK_GRANULARITY,
        DateGranularityInterface::MONTH_GRANULARITY,
    ];

    private array $filter;

    private DateTime $dateFrom;
    private DateTime $dateTo;

    /**
     * OrderStatQueryFilter constructor.
     * @param array $filter
     * @throws Exception
     */
    public function __construct(array $filter)
    {
        $this->filter = $filter;

        $this->dateFrom = (array_key_exists('from', $this->filter)
            ? new DateTime($this->filter['from'])
            : (
            (array_key_exists('date', $this->filter) && is_array($this->filter['date']) && count($this->filter) > 0)
                ? new DateTime($this->filter['date'][0])
                : new DateTime()
            ));

        $this->dateTo = (array_key_exists('to', $this->filter)
            ? new DateTime($this->filter['to'])
            : (
            (array_key_exists('date', $this->filter) && is_array($this->filter['date']) && count($this->filter) > 1)
                ? new DateTime($this->filter['date'][1])
                : new DateTime()
            ));
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return intval($this->filter['currency']['id']);
    }

    public function getDateQuery(): string
    {
        return sprintf(
            " AND updated_at >= toDateTime('%s') AND updated_at <= toDateTime('%s')",
            $this->dateFrom->format('Y-m-d H:i:s'),
            $this->dateTo->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @return string
     */
    public function getMarketQuery(): string
    {
        if (
            array_key_exists('market', $this->filter)
            && $this->filter['market'] !== null
            && is_array($this->filter['market'])
            && count($this->filter['market']) > 0
        ) {
            return sprintf(' AND market_id IN (%s) ', implode(',', Arr::pluck($this->filter['market'], 'remote_id')));
        }

        return '';
    }

    public function getWhereString(string ...$conditions): string
    {
        return implode(PHP_EOL, array_filter([...$conditions], fn($el) => $el !== ''));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getProductIdQuery(): string
    {
        return sprintf(" AND product_variant_id = '%s'",$this->getProductVariantId());
    }

    /**
     * @return string
     */
    public function getProductVariantId(): string
    {
        return $this->filter['remote_id'];
    }
}

<?php

namespace App\ClickHouse\QuickQueries;

use App\ClickHouse\DateGranularityInterface;
use App\ClickHouse\Views\OrdersTotalsToday;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class QueryHelper
{
    public const FIELD_DATE_CREATED_AT = 'created_at';
    public const FIELD_DATE_UPDATED_AT = 'updated_at';

    public const FORMAT_DATETIME = 'Y-m-d H:i:s';
    public const EMPTY_CONDITION = '';
    private static string $clickhouseDbName = '';

    /**
     * @throws Exception
     */
    public static function getDateGranularityAlias(string $granularity): string
    {
        switch ($granularity) {
            case DateGranularityInterface::EVERY_45_SECONDS_GRANULARITY:
                return 'seconds45';
            case DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY:
                return 'minutes30';
            case DateGranularityInterface::EVERY_15_MINUTES_GRANULARITY:
                return 'minutes15';
            case DateGranularityInterface::HOUR_GRANULARITY:
                return 'hour';
            case DateGranularityInterface::DAY_GRANULARITY:
                return 'date';
            case DateGranularityInterface::MONTH_GRANULARITY:
                return 'month';
            case DateGranularityInterface::WEEK_GRANULARITY:
                return 'week';
            default:
                throw new Exception(sprintf('Granularity type %s not found', $granularity));
        }
    }

    /**
     * @throws Exception
     */
    public static function getDateGranularity(string $granularity, string $field = self::FIELD_DATE_UPDATED_AT): string
    {
        switch ($granularity) {
            case DateGranularityInterface::EVERY_45_SECONDS_GRANULARITY:
                return sprintf('toStartOfInterval(%s, INTERVAL 45 second)', $field);
            case DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY:
                return sprintf('toStartOfInterval(%s, INTERVAL 30 minute)', $field);
            case DateGranularityInterface::EVERY_15_MINUTES_GRANULARITY:
                return sprintf('toStartOfInterval(%s, INTERVAL 15 minute)', $field);
            case DateGranularityInterface::HOUR_GRANULARITY:
                return sprintf('toHour(%s)', $field);
            case DateGranularityInterface::DAY_GRANULARITY:
                return sprintf('toDate(%s)', $field);
            case DateGranularityInterface::MONTH_GRANULARITY:
                return sprintf('toStartOfMonth(%s)', $field);
            case DateGranularityInterface::WEEK_GRANULARITY:
                return sprintf('toStartOfWeek(%s, 1)', $field);
            default:
                throw new Exception(sprintf('Granularity type %s not found', $granularity));
        }
    }

    public static function getPeriodConditionForPreviousInterval(
        ?Carbon $from,
        ?Carbon $to,
        string $dateField = self::FIELD_DATE_UPDATED_AT,
        string $format = self::FORMAT_DATETIME
    ): string
    {
        if ($format === null || $to === null) {
            return self::EMPTY_CONDITION;
        }

        return static::getPeriodCondition($from->clone()->subDays($from->diffInDays($to)), $from, $dateField, $format);
    }

    public static function getPeriodCondition(
        ?Carbon $from,
        ?Carbon $to,
        string $dateField = self::FIELD_DATE_UPDATED_AT,
        string $format = self::FORMAT_DATETIME
    ): string
    {
        if ($format === null || $to === null) {
            return self::EMPTY_CONDITION;
        }

        return <<<CONDITION
        {$dateField} >= toDateTime('{$from->format($format)}') AND {$dateField} <= toDateTime('{$to->format($format)}')
CONDITION;
    }

    public static function or(array $conditions): string
    {
        if (empty($value = static::cleanUpConditions($conditions))) {
            return self::EMPTY_CONDITION;
        }

        return '(' . implode(' OR ', $value) . ')';
    }

    public static function cleanUpConditions(array $conditions): array
    {
        $nonEmptyConditions = array_filter($conditions);
        if (empty($nonEmptyConditions)) {
            return [];
        }

        return $nonEmptyConditions;
    }

    public static function and($conditions): string
    {
        if (empty($value = static::cleanUpConditions($conditions))) {
            return self::EMPTY_CONDITION;
        }

        return '(' . implode(' AND ', $value) . ')';
    }

    public static function getBeforeDate(Carbon $date, string $field = 'created_at', string $format = self::FORMAT_DATETIME): string
    {
        return <<<CONDITION
        {$field} < toDateTime('{$date->format($format)}')
CONDITION;
    }

    public static function getAfterDate(Carbon $date, string $field = 'created_at', string $format = self::FORMAT_DATETIME): string
    {
        return <<<CONDITION
        {$field} > toDateTime('{$date->format($format)}')
CONDITION;
    }

    public static function iLike(string $text, string $field): string
    {
        return <<<CONDITION
        ilike({$field}, '{$text}')
CONDITION;
    }

    public static function in(string $value, array $values): string
    {
        $implodedValues = implode(', ', $values);
        return <<<CONDITION
        {$value} IN ({$implodedValues})
CONDITION;
    }

    /**
     * @param mixed $value
     * @param Closure $closure
     * @return string
     */
    public static function skipEmpty($value, Closure $closure): string
    {
        $filteredValue = array_filter($value);
        if (empty($filteredValue)) {
            return self::EMPTY_CONDITION;
        }

        return $closure($filteredValue);
    }

    public static function keyValueCondition(array $condition, bool $formatValue = true): array
    {
        $processed = [];
        foreach ($condition as $key => $value) {
            if (!empty($value) || $value === 0) {
                $processed[] = $key . ' = ' . ($formatValue ? self::formatValue($value) : $value);
            }
        }

        return $processed;
    }

    public static function formatValue($value)
    {
        if (empty($value)) {
            return '';
        }

        if (is_string($value)) {
            return "'" . $value . "'";
        }

        return $value;
    }

    public static function percentile(?float $percentile, int $currencyId): string
    {
        if (empty($percentile) || empty($currencyId)) {
            return self::EMPTY_CONDITION;
        }

        $totalsTable = OrdersTotalsToday::getName();
        $sign = $percentile >= 0.5 ? '>=' : '<=';
        $dbName = self::getDbName();

        return <<<SQL
order_id IN (
                WITH dictGet('{$dbName}.exchange_rates', 'rate',
                    tuple(currency_id, toUInt64({$currencyId}), toDate(today()))) as rate
                SELECT order_id
                FROM {$totalsTable}
                WHERE (total_profit * rate) {$sign} (
                    WITH dictGet('{$dbName}.exchange_rates', 'rate',
                        tuple(currency_id, toUInt64({$currencyId}), toDate(today()))) as rate
                    SELECT quantile({$percentile})(total_profit * rate)
                    FROM {$totalsTable}
                )
            )
SQL;
    }

    public static function getDbName(): string
    {
        if (empty(static::$clickhouseDbName)) {
            static::$clickhouseDbName = Config::get('clickhouse.dbname');
        }

        return static::$clickhouseDbName;
    }

    public static function weight(array $weight): string
    {
        $where = self::where([
            self::condition('>', 'total_weight', Arr::get($weight, 'greater_than')),
            self::condition('<', 'total_weight', Arr::get($weight, 'lower_than'))
        ]);

        if (empty($where)) {
            return self::EMPTY_CONDITION;
        }

        $totalsTable = OrdersTotalsToday::getName();

        return <<<SQL
                order_id IN (
                    SELECT order_id
                    FROM {$totalsTable}
                    {$where}
                )
SQL;
    }

    /**
     * @param array $conditions
     * @return string
     * @todo Multi-level condition with or, and statements
     */
    public static function where(array $conditions): string
    {
        if (empty($nonEmptyConditions = static::cleanUpConditions($conditions))) {
            return self::EMPTY_CONDITION;
        }

        return 'WHERE ' . implode(' AND ', $nonEmptyConditions);
    }

    /**
     * @param array $conditions
     * @return string
     */
    public static function having(array $conditions): string
    {
        if (empty($nonEmptyConditions = static::cleanUpConditions($conditions))) {
            return self::EMPTY_CONDITION;
        }

        return 'HAVING ' . implode(' AND ', $nonEmptyConditions);
    }


    /**
     * QueryHelper::condition('>', 'price', 200.00);
     * @param string $condition > = < <>
     * @param string $field
     * @param string|int|float $value
     * @return string
     */
    public static function condition(string $condition, string $field, $value): string
    {
        if (empty($value)) {
            return self::EMPTY_CONDITION;
        }

        return <<<CONDITION
            {$field} {$condition} {$value}
CONDITION;
    }
}

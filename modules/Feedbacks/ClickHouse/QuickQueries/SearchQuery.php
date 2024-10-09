<?php

namespace Modules\Feedbacks\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\Feedback;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;

class SearchQuery extends BaseQuickQuery
{
    /**
     * @var array|int[]
     */
    private array $marketIds;

    /**
     * @var array|int[]
     */
    private array $sourceIds;

    /**
     * @var array|string[]
     */
    private array $words;

    private Carbon $from;

    public function __construct(array $marketIds, array $sourceIds, array $words, Carbon $from)
    {
        $this->marketIds = $marketIds;
        $this->sourceIds = $sourceIds;
        $this->words = $words;
        $this->from = $from;
    }

    /**
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(Feedback::class);

        $dbName = QH::getDbName();

        $where = QH::where([
            QH::getBeforeDate($this->from),
            QH::skipEmpty(array_filter($this->words), function ($value) {
                return QH::or(array_map(
                    fn($value) => QH::iLike('%' . $value . '%', 'message'),
                    $value
                ));
            }),
            QH::skipEmpty($this->marketIds, fn($value) => QH::in('market_id', $value)),
            QH::skipEmpty($this->sourceIds, fn($value) => QH::in('source_id', $value)),
        ]);

        return <<<SQL
            SELECT
                   unique_id,
                   name,
                   message,
                   created_at,
                   market_id,
                   dictGet('{$dbName}.markets', 'name', market_id) as market_name,
                   dictGet('{$dbName}.markets', 'icon_link', market_id)                    as market_icon_link,
                   source_id,
                   dictGet('{$dbName}.sources', 'name', source_id) as source_name,
                   dictGet('{$dbName}.sources', 'icon_link', source_id)                    as source_icon_link,
                   url
            FROM {$tableName}
            {$where}
                ORDER BY created_at DESC
            LIMIT 50
SQL;
    }
}

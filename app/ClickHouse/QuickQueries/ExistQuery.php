<?php

namespace App\ClickHouse\QuickQueries;

class ExistQuery extends BaseQuickQuery
{
    private string $table;
    private array $condition;

    public function __construct(string $table, array $condition)
    {
        $this->table = $table;
        $this->condition = $condition;
    }

    public function __toString(): string
    {
        $where = QueryHelper::where($this->condition);

        return <<<SQL
        SELECT 1 FROM {$this->table} {$where} LIMIT 1
SQL;
    }
}

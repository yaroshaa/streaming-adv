<?php

namespace App\ClickHouse\Repositories;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\QuickQueries\ExistQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use App\ClickHouse\Repository;
use App\ClickHouse\RepositoryInterface;

class BaseFeedbackRepository extends Repository implements RepositoryInterface
{
    /**
     * @param string $uniqueId
     * @return bool
     * @throws ClickHouseException
     */
    public function exist(string $uniqueId): bool
    {
        return !empty($this->quickQuery(new ExistQuery(
            $this->tableName(),
            QueryHelper::keyValueCondition(['unique_id' => $uniqueId])
        )));
    }
}

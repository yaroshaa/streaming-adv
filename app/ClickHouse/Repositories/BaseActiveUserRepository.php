<?php


namespace App\ClickHouse\Repositories;


use App\ClickHouse\ClickHouseException;
use App\ClickHouse\QuickQueries\ExistQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use App\ClickHouse\Repository;
use App\ClickHouse\RepositoryInterface;

class BaseActiveUserRepository extends Repository implements RepositoryInterface
{
    /**
     * @param array $conditions
     * @return bool
     * @throws ClickHouseException
     */
    public function exist(array $conditions): bool
    {
        return !empty($this->quickQuery(new ExistQuery(
            $this->tableName(),
            QueryHelper::keyValueCondition($conditions, false)
        )));
    }
}

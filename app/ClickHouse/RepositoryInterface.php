<?php


namespace App\ClickHouse;


interface RepositoryInterface
{
    /**
     * @param string $modelClass
     */
    public function setModelClass(string $modelClass): void;

    /**
     * @return string
     */
    public function getModelClass(): string;

    /**
     * @param ClickHouseModel $model
     */
    public function persist(ClickHouseModel $model): void;

    /**
     *
     */
    public function flush(): void;
}

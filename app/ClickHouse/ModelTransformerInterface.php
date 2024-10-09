<?php


namespace App\ClickHouse;


use Generator;

interface ModelTransformerInterface
{
    /**
     * @param string $modelClass
     * @return void
     */
    public function setModelClass(string $modelClass): void;

    /**
     * @return string
     */
    public function getModelClass(): string;

    /**
     * @param ModelTransformersBag $bag
     */
    public function setModelTransformerBag(ModelTransformersBag $bag): void;

    /**
     * @param array $data
     * @return ClickHouseModel[]|ModelCollection
     */
    public function fromArrayToObjects(array $data): ModelCollection;

    /**
     * @param ClickHouseModel $model
     * @return array
     */
    public function fromObjectToArray(ClickHouseModel $model): Generator;
}

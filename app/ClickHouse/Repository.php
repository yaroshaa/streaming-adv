<?php


namespace App\ClickHouse;


use App\ClickHouse\QuickQueries\BaseQuickQuery;
use ClickHouseDB\Client;
use ClickHouseDB\Statement;

class Repository implements RepositoryInterface
{
    /**
     * @var Client
     */
    private Client $db;

    /**
     * @var array
     */
    private array $values = [];

    /**
     * @var ModelTransformersBag
     */
    private ModelTransformersBag $bag;

    /**
     * @var string
     */
    private string $modelClass;

    /**
     * @var QueryBag
     */
    private QueryBag $queryBag;

    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $config;

    /**
     * @param ModelTransformersBag $modelTransformersBag
     * @param Client $db
     * @param QueryBag $queryBag
     * @param ClickhouseConfig $config
     */
    public function __construct(ModelTransformersBag $modelTransformersBag, Client $db, QueryBag $queryBag, ClickhouseConfig $config)
    {
        $this->db = $db;
        $this->bag = $modelTransformersBag;
        $this->queryBag = $queryBag;
        $this->config = $config;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param ClickHouseModel $model
     * @throws ClickHouseException
     */
    public function persist(ClickHouseModel $model): void
    {
        foreach ($this->bag->getTransformer(get_class($model))->fromObjectToArray($model) as $value) {
            $this->values[] = $value;
        }
    }

    /**
     * @throws ClickHouseException
     */
    public function flush(): void
    {
        if (count($this->values) === 0) {
            throw new ClickHouseException('Nothing to push');
        }

        $this->db->insert($this->config->getTableName($this->modelClass), $this->values, array_keys($this->values[0]));
        $this->values = [];
    }

    /**
     * @param string $query
     * @return ClickHouseModel[]|ModelCollection
     * @throws ClickHouseException
     */
    protected function select(string $query): ModelCollection
    {
        $data = $this->db->select($query)->rows();
        return $this->getTransformer($this->modelClass)->fromArrayToObjects($data);
    }

    /**
     * @param string $queryClass
     * @return QueryInterface
     * @throws ClickHouseException
     */
    protected function getQuery(string $queryClass): QueryInterface
    {
        return $this->queryBag->getQuery($queryClass);
    }

    /**
     * @param string $modelClass
     * @return ModelTransformerInterface
     * @throws ClickHouseException
     */
    protected function getTransformer(string $modelClass): ModelTransformerInterface
    {
        return $this->bag->getTransformer($modelClass);
    }

    /**
     * @return Client
     */
    protected function getConnection(): Client
    {
        return $this->db;
    }

    /**
     * @param string $query
     * @return array
     */
    public function getArrayResult(string $query): array
    {
        return $this->db->select($query)->rows();
    }

    /**
     * @param array $values
     * @return Statement
     * @throws ClickHouseException
     */
    public function insert(array $values): Statement
    {
        return $this->db->insertAssocBulk($this->config->getTableName($this->modelClass), [$values]);
    }

    /**
     * @return string
     * @throws ClickHouseException
     */
    public function tableName(): string
    {
        return $this->config->getTableName($this->modelClass);
    }

    /**
     * @param string $query
     * @return bool
     */
    public function existByQuery(string $query): bool
    {
        return !empty($this->getArrayResult($query));
    }

    /**
     * @param BaseQuickQuery $query
     * @return array
     */
    public function quickQuery(BaseQuickQuery $query): array
    {
        $query->setClickhouseConfig($this->config);

        return $this->getArrayResult($query);
    }
}

<?php


namespace App\ClickHouse;


class QueryBag
{
    /**
     * @var array
     */
    private array $queries = [];

    /**
     * QueryBag constructor.
     * @param QueryInterface ...$queries
     */
    public function __construct(QueryInterface ...$queries)
    {
        foreach ($queries as $query) {
            $this->queries[get_class($query)] = $query;
        }
    }

    /**
     * @param string $queryClass
     * @return QueryInterface
     * @throws ClickHouseException
     */
    public function getQuery(string $queryClass): QueryInterface
    {
        if (!array_key_exists($queryClass, $this->queries)) {
            throw new ClickHouseException(sprintf('Query "%s" not found', $queryClass));
        }

        return $this->queries[$queryClass];
    }
}

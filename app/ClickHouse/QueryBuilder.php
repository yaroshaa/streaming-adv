<?php


namespace App\ClickHouse;


class QueryBuilder
{
    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $config;

    /**
     * QueryBuilder constructor.
     * @param ClickhouseConfig $config
     */
    public function __construct(ClickhouseConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $modelClass
     * @param array $params
     * @return string
     * @throws ClickHouseException
     */
    private function where(string $modelClass, array $params): string
    {
        $where = '';

        foreach ($params as $field => $condition) {
            $where .= ($where ? 'AND WHERE ' : 'WHERE ') . $this->config->getFieldColumn($modelClass, $field) . $condition;
        }

        return $where;
    }

    /**
     * @param int $limit
     * @return string
     */
    private function getLimit(int $limit): string
    {
        return $limit ? sprintf('LIMIT %d', $limit) : '';
    }

    /**
     * @param string $modelClass
     * @param array $params
     * @return string
     * @throws ClickHouseException
     */
    private function getOrder(string $modelClass, array $params): string
    {
        $order = [];

        foreach ($params as $field => $dir) {
            $order[] = $this->config->getFieldColumn($modelClass, $field) . ' ' . $dir;
        }

        return count($order) === 0 ? '' : 'ORDER BY ' . implode(', ', $order);
    }
}

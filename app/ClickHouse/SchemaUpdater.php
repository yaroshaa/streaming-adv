<?php


namespace App\ClickHouse;


use ClickHouseDB\Client;
use Exception;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class SchemaUpdater
{
    /**
     * @var Client
     */
    private Client $db;
    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $config;
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    /**
     * SchemaUpdater constructor.
     * @param Client $db
     * @param ClickhouseConfig $config
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(Client $db, ClickhouseConfig $config, QueryBuilder $queryBuilder)
    {
        $this->db = $db;
        $this->config = $config;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return Generator
     * @throws ClickHouseException
     * @throws Exception
     */
    public function update(): Generator
    {
        foreach ($this->config->getModels() as $modelClass) {
            $query = $this->getSql($modelClass);

            if (null === $query) {
                yield sprintf('Table %s not changed', $this->config->getTableName($modelClass));
            }

            $this->db->write($query);
            yield sprintf('Table %s updated', $this->config->getTableName($modelClass));
        }
    }

    public function getSql(string $modelClass): ?string
    {
        if ($this->config->hasConfig($modelClass, 'virtual') && $this->config->getConfig($modelClass, 'virtual')) {
            return null; // skip if it is virtual model
        }

        $columns = $this->getColumns($modelClass);

        $tableName = $this->config->getTableName($modelClass);

        if ($this->db->isExists(Config::get('clickhouse.dbname'), $tableName)) {
            $query = $this->combineUpdateQuery($tableName, ...$this->getColumnsToModify($tableName, $columns));
        } else {
            $query = sprintf(
            /** @lang text */ 'CREATE TABLE %s (%s) engine=%s(%s) %s',
                $tableName,
                implode(', ', array_map(fn($type, $field) => sprintf('%s %s', $field, $type), $columns, array_keys($columns))),
                $this->config->getTableEngine($modelClass),
                $this->config->getTableKeys($modelClass) ? implode(', ', $this->config->getTableKeys($modelClass)) : '',
                $this->config->getTableOrder($modelClass) ? 'ORDER BY (' . implode(', ', $this->config->getTableOrder($modelClass)) . ')' : ''
            );
        }

        return $query;
    }

    public function revertSql(string $modelClass): ?string
    {
        if ($this->config->hasConfig($modelClass, 'virtual') && $this->config->getConfig($modelClass, 'virtual')) {
            return null; // skip if it is virtual model
        }

        $columns = $this->getColumns($modelClass);

        $tableName = $this->config->getTableName($modelClass);

        if ($this->db->isExists(Config::get('clickhouse.dbname'), $tableName)) {
            list($delete, $update, $add) = $this->getColumnsToModify($tableName, $columns);

            $columnsNow = $this->db->select(sprintf('DESCRIBE TABLE %s', $tableName))->rows();
            foreach ($update as $col => &$type) {
                $filter = array_filter($columnsNow, fn ($row) => $row['name'] === $col);
                $type = array_shift($filter)['type'];
            }

            $query = $this->combineUpdateQuery($tableName, $add, $update, $delete);

        } else {
            $query = sprintf(
            /** @lang text */ 'DROP TABLE %s', $tableName);
        }

        return $query;
    }

    /**
     * @param bool $rewrite
     * @return Generator
     * @throws ClickHouseException
     */
    public function dictInit(bool $rewrite = false): Generator
    {
        $query = "
            CREATE DICTIONARY %name% (
                %fields%
            )
            PRIMARY KEY %pk%
            LAYOUT(%layout%)
            %lifetime%
            SOURCE(MYSQL(
                port %source.mysql.port%
                host '%source.mysql.host%'
                user '%source.mysql.user%'
                password '%source.mysql.password%'
                db '%source.mysql.db%'
                table '%source.mysql.table%'
                invalidate_query '%source.mysql.invalidate_query%'
            ));
        ";

        preg_match_all('/%(.+)%/', $query, $templates);

        foreach ($this->config->getDictionaries() as $dictionary) {
            if ($this->db->select("SELECT name FROM system.dictionaries WHERE name = :name", ['name' => $dictionary])->count()) {
                if (!$rewrite) {
                    yield sprintf('Dictionary %s is already exists, skipping...', $dictionary);
                    continue;
                }

                $this->db->write(sprintf('DROP dictionary %s', $dictionary));
                yield sprintf('Dictionary %s dropped', $dictionary);
            }
            $stmt = str_replace($templates[0], array_map(fn($config) => $this->config->getDictionaryConfig($dictionary, $config), $templates[1]), $query);
            $this->db->write($stmt);
            yield sprintf('Dictionary %s created', $dictionary);
        }
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @return array
     */
    private function getColumnsToModify(string $tableName, array $columns): array
    {
        $rows = $this->db->select(sprintf('DESCRIBE TABLE %s', $tableName))->rows();
        $current = array_combine(Arr::pluck($rows, 'name'), Arr::pluck($rows, 'type'));

        $add = [];
        $update = [];

        foreach ($columns as $column => $type) {
            if (array_key_exists($column, $current)) {
                if ($type === $current[$column]) {
                    continue;
                }

                $update[$column] = $type;
                continue;
            }

            $add[$column] = $type;
        }

        $delete = array_filter($current, fn($type, $field) => !array_key_exists($field, $columns), ARRAY_FILTER_USE_BOTH);
        return array($add, $update, $delete);
    }

    /**
     * @param string $modelClass
     * @return array
     * @throws ClickHouseException
     */
    private function getColumns(string $modelClass): array
    {
        $fields = $this->config->getFields($modelClass, false);
        $columns = [];

        foreach ($fields as $column) {
            $nested = $this->config->getFieldConfig($modelClass, $column, 'nested', false);

            if (!$nested) {
                $columns[$this->config->getFieldColumn($modelClass, $column)] = $this->config->getFieldType($modelClass, $column);

                continue;
            }

            $nestedFields = $this->config->getFields($nested, false);

            $nestedColumns = array_combine(
                array_map(fn($f) => $this->config->getFieldColumn($nested, $f), $nestedFields),
                array_map(fn($f) => $this->config->getFieldType($nested, $f), $nestedFields)
            );

            $columns = array_merge($columns, $nestedColumns);
        }
        return $columns;
    }

    private function combineUpdateQuery(string $tableName, array $add, array $update, array $delete): string
    {
        $addQuery = $add ? implode(', ', array_map(fn($type, $field) => sprintf(' ADD COLUMN %s %s ', $field, $type), $add, array_keys($add))) : '';
        $deleteQuery = $delete ? implode(', ', array_map(fn($field) => sprintf(' DROP COLUMN %s ', $field), array_keys($delete))) : '';
        $updateQuery = $update ? implode(', ', array_map(fn($type, $field) => sprintf(' MODIFY COLUMN %s %s ', $field, $type), $update, array_keys($update))) : '';

        if (!$addQuery && !$deleteQuery && !$updateQuery) {
            return '';
        }

        return "ALTER TABLE $tableName " . implode(' ,', array_filter([$deleteQuery, $addQuery, $updateQuery]));
    }
}

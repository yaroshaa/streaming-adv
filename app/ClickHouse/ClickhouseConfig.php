<?php


namespace App\ClickHouse;


use Illuminate\Support\Facades\Config;

class ClickhouseConfig
{
    /**
     * Clickhouse mapping config key
     */
    public const CLICKHOUSE_MAPPING_KEY = 'clickhouse.mapping';

    /**
     * Clickhouse dictionaries config key
     */
    public const CLICKHOUSE_DICTIONARIES_KEY = 'clickhouse.dictionaries';

    /**
     * @var array
     */
    private array $fields = [];

    /**
     * Get fields
     * @param string $modelClass
     * @param bool $withManual
     * @return array
     * @throws ClickHouseException
     */
    public function getFields(string $modelClass, $withManual = true): array
    {
        if (!array_key_exists($modelClass, $this->fields)) {
            $fieldsConfig = $this->getConfig($modelClass, 'fields');

            if ($withManual === false) {
                $fieldsConfig = array_filter($fieldsConfig, fn(array $config) => !array_key_exists('manual', $config) || $config['manual'] == false);
            }

            $this->fields[$modelClass] = array_keys($fieldsConfig);
        }

        return $this->fields[$modelClass];
    }

    /**
     * @param string $modelClass
     * @param string $field
     * @param string $config
     * @return bool
     */
    public function hasFieldConfig(string $modelClass, string $field, string $config): bool
    {
        return $this->hasConfig($modelClass, sprintf('fields.%s.%s', $field, $config));
    }

    /**
     * @param string $modelClass
     * @param string $field
     * @param string $config
     * @param null $default
     * @return mixed
     * @throws ClickHouseException
     */
    public function getFieldConfig(string $modelClass, string $field, string $config, $default = null)
    {
        if ($default !== null) {
            return $this->hasFieldConfig($modelClass, $field, $config) ? $this->getConfig($modelClass, sprintf('fields.%s.%s', $field, $config)) : $default;
        }

        return $this->getConfig($modelClass, sprintf('fields.%s.%s', $field, $config));
    }

    /**
     * @param string $modelClass
     * @param string $field
     * @return string
     * @throws ClickHouseException
     */
    public function getFieldColumn(string $modelClass, string $field): string
    {
        $key = sprintf('fields.%s.column', $field);

        if (!$this->hasConfig($modelClass, $key)) {
            throw new ClickHouseException(sprintf('Column %s for model %s is not set', $field, $modelClass));
        }

        return $this->getConfig($modelClass, $key);
    }

    /**
     * @param string $modelClass
     * @param string $field
     * @return string
     * @throws ClickHouseException
     */
    public function getFieldType(string $modelClass, string $field): string
    {
        return $this->getConfig($modelClass, sprintf('fields.%s.type', $field));
    }

    /**
     * @param string $modelClass
     * @return string
     * @throws ClickHouseException
     */
    public function getTableName(string $modelClass): string
    {
        return $this->getConfig($modelClass, 'table');
    }

    /**
     * @param string $modelClass
     * @param string $category
     * @param null $default
     * @return mixed
     */
    public function getConfig(string $modelClass, string $category, $default = null)
    {
        $path = sprintf('%s.%s.%s', ClickhouseConfig::CLICKHOUSE_MAPPING_KEY, $modelClass, $category);

        return Config::has($path) ? Config::get($path) : $default;
    }

    /**
     * @param string $modelClass
     * @param string $category
     * @return mixed
     * @throws ClickHouseException
     */
    private function tryChildren(string $modelClass, string $category)
    {
        $merge = sprintf('%s.%s.merge', ClickhouseConfig::CLICKHOUSE_MAPPING_KEY, $modelClass);

        if (Config::has($merge)) {
            return $this->getConfig(Config::get($merge), $category);
        }

        throw new ClickHouseException(sprintf('Config "%s" does not set for the model "%s"', $category, $modelClass));
    }

    /**
     * @param string $modelClass
     * @param string $category
     * @return bool
     */
    public function hasConfig(string $modelClass, string $category): bool
    {
        return Config::has(sprintf('%s.%s.%s', ClickhouseConfig::CLICKHOUSE_MAPPING_KEY, $modelClass, $category));
    }

    /**
     * @return array
     * @throws ClickHouseException
     */
    public function getModels(): array
    {
        if (!Config::has(ClickhouseConfig::CLICKHOUSE_MAPPING_KEY)) {
            throw new ClickHouseException('Clickhouse mapping is not set or mapping key is invalid');
        }

        $mappings = Config::get(ClickhouseConfig::CLICKHOUSE_MAPPING_KEY);

        if (count($mappings) === 0) {
            throw new ClickHouseException('Clickhouse mapping is empty');
        }

        return array_keys($mappings);
    }

    /**
     * @param string $modelClass
     * @return string
     */
    public function getTableEngine(string $modelClass): string
    {
        return $this->getConfig($modelClass, 'engine');
    }

    /**
     * @param string $modelClass
     * @return array
     */
    public function getTableKeys(string $modelClass): array
    {
        return $this->hasConfig($modelClass, 'table_keys') ? $this->getConfig($modelClass, 'table_keys') : [];
    }

    /**
     * @param string $modelClass
     * @return array
     */
    public function getTableOrder(string $modelClass): array
    {
        if (!$this->hasConfig($modelClass, 'order_by')) {
            return [];
        }

        return $this->getConfig($modelClass, 'order_by');
    }

    /**
     * @return array
     * @throws ClickHouseException
     */
    public function getDictionaries(): array
    {
        if (!Config::has(ClickhouseConfig::CLICKHOUSE_DICTIONARIES_KEY)) {
            throw new ClickHouseException('Dictionaries are not configured');
        }

        return array_keys(Config::get(ClickhouseConfig::CLICKHOUSE_DICTIONARIES_KEY));
    }

    /**
     * @param string $dictionary
     * @param string $config
     * @return string
     * @throws ClickHouseException
     */
    public function getDictionaryConfig(string $dictionary, string $config): string
    {
        if ($config === 'fields') {
            return $this->getDictionaryFields($dictionary);
        }

        $key = $this->getDictionaryKey($dictionary) . '.' . $config;

        if ($config === 'lifetime') {
            return $this->hasDictionaryConfig($dictionary, 'lifetime') ? sprintf('LIFETIME(%d)', Config::get($key)) : '';
        }

        if (!Config::has($key)) {
            throw new ClickHouseException(sprintf('Config %s for the %s is not specified', $config, $dictionary));
        }


        return Config::get($key);
    }

    /**
     * @param string $dictionary
     * @param string $config
     * @return bool
     * @throws ClickHouseException
     */
    private function hasDictionaryConfig(string $dictionary, string $config): bool
    {
        return Config::has($this->getDictionaryKey($dictionary) . '.' . $config);
    }

    /**
     * @param string $dictionary
     * @return string
     * @throws ClickHouseException
     */
    protected function getDictionaryFields(string $dictionary): string
    {
        $key = $this->getDictionaryKey($dictionary) . '.fields';

        if (!Config::has($key)) {
            throw new ClickHouseException(sprintf('Fields %s for dictionary not configured', $dictionary));
        }

        $dictFields = Config::get($key);


        return implode(', ' . PHP_EOL, array_map(fn(string $type, string $field) => $field . ' ' . $type, $dictFields, array_keys($dictFields)));
    }

    /**
     * @param string $dictionary
     * @return string
     * @throws ClickHouseException
     */
    private function getDictionaryKey(string $dictionary): string
    {
        $key = ClickhouseConfig::CLICKHOUSE_DICTIONARIES_KEY . '.' . $dictionary;

        if (!Config::has($key)) {
            throw new ClickHouseException(sprintf('Dictionary %s not configured', $dictionary));
        }

        return $key;
    }
}

<?php


namespace App\ClickHouse;


use Generator;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;

class ModelTransformer implements ModelTransformerInterface
{
    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $config;

    /**
     * @var Converter
     */
    private Converter $converter;

    /**
     * ModelTransformer constructor.
     * @param ClickhouseConfig $config
     * @param Converter $converter
     */
    public function __construct(ClickhouseConfig $config, Converter $converter)
    {
        $this->config = $config;
        $this->converter = $converter;
    }

    /**
     * @var string
     */
    private string $modelClass;

    /**
     * @var ModelTransformersBag
     */
    protected ModelTransformersBag $bag;

    /**
     * @param string $modelClass
     * @throws ClickHouseException
     */
    public function setModelClass(string $modelClass): void
    {
        if (!class_exists($modelClass)) {
            throw new ClickHouseException(sprintf('Class "%s" does not exists', $modelClass));
        }

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
     * @param ModelTransformersBag $bag
     */
    public function setModelTransformerBag(ModelTransformersBag $bag): void
    {
        $this->bag = $bag;
    }

    /**
     * @param array $data
     * @return ClickHouseModel[]|ModelCollection
     * @throws ClickHouseException
     */
    public function fromArrayToObjects(array $data): ModelCollection
    {
        return new ModelCollection(array_map(fn($row) => $this->createEntity($row), $data));
    }

    /**
     * @param ClickHouseModel $model
     * @return Generator
     * @throws ClickHouseException
     * @throws ReflectionException
     */
    public function fromObjectToArray(ClickHouseModel $model): Generator
    {
        if (!$this->modelClass) {
            throw new ClickHouseException('Model class is not set');
        }

        if (!$model instanceof $this->modelClass) {
            throw new ClickHouseException(sprintf('Use transformer of class "%s"', get_class($model)));
        }

        $result = [];
        $reflection = new ReflectionClass($this->modelClass);

        foreach ($reflection->getProperties() as $property) {
            if ($this->config->getFieldConfig($this->modelClass, $property->getName(), 'manual', false)) {
                continue;
            }

            $method = 'get' . ucfirst($property->getName());
            if ($nested = $this->config->getFieldConfig($this->modelClass, $property->getName(), 'nested', false)) {
                if (false === $this->config->getFieldConfig($this->modelClass, $property->getName(), 'fillable', true)) {
                    continue; // skip not fillable fields
                }

                foreach ($this->bag->getTransformer($nested)->fromObjectToArray(call_user_func_array([$model, $method], [])) as $nestedResults) {
                    $result = array_merge($result, $nestedResults);
                };
                continue;
            }

            $column = $this->config->getFieldColumn($this->modelClass, $property->getName());
            $result[$column] = call_user_func_array([$model, $method], []);
        }

        yield $result;
    }

    /**
     * @param $row
     * @return ClickHouseModel
     * @throws ClickHouseException
     */
    private function createEntity(array $row): ClickHouseModel
    {
        $entity = new $this->modelClass();

        foreach ($this->config->getFields($this->modelClass) as $field) {
            if (!$this->config->getFieldConfig($this->modelClass, $field, 'fillable', true)) {
                continue;
            }

            if ($nested = $this->config->getFieldConfig($this->modelClass, $field, 'nested', false)) {
                $values = $this->bag->getTransformer($nested)->fromArrayToObjects([$row]);
                call_user_func_array([$entity, 'set' . ucfirst($field)], [$values->first()]);

                continue;
            }

            $nullable = $this->config->getFieldConfig($this->modelClass, $field, 'nullable', false);

            $fieldColumn = $this->config->getFieldColumn($this->modelClass, $field);

            if (!array_key_exists($fieldColumn, $row) && !$nullable) {
                throw new ClickHouseException(sprintf('Value %s for filling model %s is not provided', $fieldColumn, $this->modelClass));
            }

            $value = Arr::get($row, $fieldColumn);

            if ($this->config->hasFieldConfig($this->modelClass, $field, 'converter')) {
                $value = $this->converter->convert($value, $this->config->getFieldConfig($this->modelClass, $field, 'converter'));
            }

            call_user_func_array([$entity, 'set' . ucfirst($field)], [$value]);
        }

        return $entity;
    }

    /**
     * @return ClickhouseConfig
     */
    protected function getConfig(): ClickhouseConfig
    {
        return $this->config;
    }
}

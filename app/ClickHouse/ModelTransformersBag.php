<?php


namespace App\ClickHouse;


class ModelTransformersBag
{
    /**
     * @var ModelTransformerInterface[]
     */
    private array $transformers = [];

    /**
     * ModelTransformersBag constructor.
     * @param ModelTransformerInterface ...$transformers
     */
    public function __construct(ModelTransformerInterface ...$transformers)
    {
        foreach ($transformers as $transformer) {
            $transformer->setModelTransformerBag($this);
            $this->transformers[$transformer->getModelClass()] = $transformer;
        }
    }

    /**
     * @param string $modelClass
     * @return ModelTransformerInterface
     * @throws ClickHouseException
     */
    public function getTransformer(string $modelClass): ModelTransformerInterface
    {
        if (!array_key_exists($modelClass, $this->transformers)) {
            throw new ClickHouseException(sprintf('Transformer "%s" not found', $modelClass));
        }

        return $this->transformers[$modelClass];
    }
}

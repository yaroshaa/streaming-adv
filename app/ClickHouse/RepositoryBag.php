<?php


namespace App\ClickHouse;


class RepositoryBag
{
    /**
     * @var RepositoryInterface[]
     */
    private array $repositories = [];

    /**
     * RepositoryBag constructor.
     * @param RepositoryInterface ...$repositories
     */
    public function __construct(RepositoryInterface ...$repositories)
    {
        foreach ($repositories as $repository) {
            $this->repositories[$repository->getModelClass()] = $repository;
        }
    }

    /**
     * @param string $modelClass
     * @return RepositoryInterface
     * @throws ClickHouseException
     */
    public function getRepository(string $modelClass): RepositoryInterface
    {
        if (!array_key_exists($modelClass, $this->repositories)) {
            throw new ClickHouseException(sprintf('Repository "%s" not found', $modelClass));
        }

        return $this->repositories[$modelClass];
    }
}

<?php


namespace App\Repositories;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionClass;
use ReflectionException;

class ChildEntityRepository extends EntityRepository
{
    /**
     * @param int $id
     * @param array $payload
     * @return mixed|object
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function findOrCreate(int $id, array $payload = [])
    {
        return $this->find($id) ?? $this->create($payload);
    }

    /**
     * @param array $criteria
     * @param array $payload
     * @param bool $flush
     * @return mixed|object
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function findOneByOrCreate(array $criteria, array $payload, bool $flush = true)
    {
        return $this->findOneBy($criteria) ?? $this->create($payload, $flush);
    }

    /**
     * @param array $payload
     * @param bool $flush
     * @return mixed
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function create(array $payload, bool $flush = true)
    {
        $entityClass = $this->getEntityName();

        $obj = new $entityClass;
        $reflection = new ReflectionClass($entityClass);
        foreach ($payload as $field => $value) {
            if (!$reflection->hasProperty($field)) {
                throw new InvalidArgumentException(sprintf('%s->%s is not exists', $entityClass, $field));
            }

            $setter = 'set' . ucfirst($field);

            if (!$reflection->hasMethod($setter)) {
                throw new Exception(sprintf('Please provide setter for the %s->%s', $entityClass, $field));
            }

            call_user_func_array([$obj, $setter], [$value]);
        }

        if ($flush) {
            $this->_em->persist($obj);
            $this->_em->flush();
        }

        return $obj;
    }
}

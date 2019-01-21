<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;

class ProviderFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $class
     * @param array $sort
     *
     * @return EntityListProvider
     */
    public function createEntityListProvider(string $class, array $sort = []) : EntityListProvider
    {
        return new EntityListProvider($this->entityManager, $class, $sort);
    }

    /**
     * @param string $class
     *
     * @return ORMQueryBuilderProvider
     */
    public function createOrmProvider(string $class) : ORMQueryBuilderProvider
    {
        return new $class($this->entityManager);
    }

    /**
     * @param string $class
     *
     * @return DBALQueryBuilderProvider
     */
    public function createDbalProvider(string $class) : DBALQueryBuilderProvider
    {
        return new $class($this->entityManager);
    }

    /**
     * @param string $class
     *
     * @return NativeQueryProvider
     */
    public function createNativeQueryProvider(string $class) : NativeQueryProvider
    {
        return new $class($this->entityManager);
    }

    public function createArrayProvider(string $class) : ArrayProvider
    {
        return new $class();
    }
}

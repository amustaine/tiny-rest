<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;

class ProviderFactory
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function createEntityListProvider(string $class, array $sort = []) : EntityListProvider
    {
        return new EntityListProvider($this->entityManager, $class, $sort);
    }

    public function createOrmProvider(string $class) : ORMQueryBuilderProvider
    {
        return new $class($this->entityManager);
    }

    public function createDbalProvider(string $class) : DBALQueryBuilderProvider
    {
        return new $class($this->entityManager);
    }

    public function createNativeQueryProvider(string $class) : NativeQueryProvider
    {
        return new $class($this->entityManager);
    }

    public function createArrayProvider(string $class) : ArrayProvider
    {
        return new $class();
    }
}

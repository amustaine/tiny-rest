<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class DBALQueryBuilderProvider implements ProviderInterface
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
     * @param TransferObjectInterface|null $transferObject
     *
     * @return QueryBuilder
     */
    abstract public function getData(TransferObjectInterface $transferObject) : QueryBuilder;

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}

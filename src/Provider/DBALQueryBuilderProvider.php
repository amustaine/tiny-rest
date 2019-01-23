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
    abstract public function provide(TransferObjectInterface $transferObject) : QueryBuilder;

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return array
     */
    public function toArray(TransferObjectInterface $transferObject) : array
    {
        return $this->provide($transferObject)->execute()->fetchAll();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}

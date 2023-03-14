<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class DBALQueryBuilderProvider implements ProviderInterface
{
    use SortTrait, FilterTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    abstract public function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder;

    public function provide() : QueryBuilder
    {
        $qb = $this->getQueryBuilder($this->filter);

        if ($this->validSort()) {
            $this->applySort($qb);
        }

        return $qb;
    }

    public function toArray() : array
    {
        return $this->provide()->execute()->fetchAllAssociative();
    }

    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    public function setFilter(object $transferObject) : void
    {
        $this->filter = $transferObject;
    }

    protected function applySort(QueryBuilder $queryBuilder) : void
    {
        $queryBuilder->addOrderBy($this->sort->getField(), $this->sort->getSortDir());
    }
}

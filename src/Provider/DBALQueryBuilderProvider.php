<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class DBALQueryBuilderProvider implements ProviderInterface
{
    use SortTrait, FilterTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder;

    public function provide(): QueryBuilder
    {
        $qb = $this->getQueryBuilder($this->filter);

        if ($this->validSort()) {
            $this->applySort($qb);
        }

        return $qb;
    }

    public function toArray(): array
    {
        return $this->provide()->execute()->fetchAll();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    public function setFilter(object $transferObject)
    {
        $this->filter = $transferObject;
    }

    protected function applySort(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy($this->sort->getField(), $this->sort->getSortDir());
    }
}

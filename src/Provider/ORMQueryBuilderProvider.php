<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class ORMQueryBuilderProvider implements ProviderInterface
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

    public function provide() : QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder($this->filter);

        if ($this->sort && $this->sort->getField()) {
            $this->applySort($queryBuilder);
        }

        return $queryBuilder;
    }

    public function toArray(): array
    {
        return $this->provide()->getQuery()->getResult();
    }

    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->createQueryBuilder();
    }

    protected function applySort(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy($this->sort->getField(), $this->sort->getSortDir());
    }
}

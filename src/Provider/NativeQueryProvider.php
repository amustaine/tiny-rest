<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use TinyRest\QueryBuilder\NativeQueryBuilder;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class NativeQueryProvider implements ProviderInterface
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

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return ResultSetMapping
     */
    abstract protected function getRsm(TransferObjectInterface $transferObject) : ResultSetMapping;

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return QueryBuilder
     */
    abstract protected function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder;

    public function provide(): NativeQueryBuilder
    {
        $qb = $this->getQueryBuilder($this->filter);

        if ($this->sort && $this->sort->getField()) {
            $this->applySort($qb);
        }

        return new NativeQueryBuilder($this->entityManager, $qb, $this->getRsm($this->filter));
    }

    public function toArray(): array
    {
        return $this->provide()->createNativeQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function applySort(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy($this->sort->getField(), $this->sort->getSortDir());
    }
}

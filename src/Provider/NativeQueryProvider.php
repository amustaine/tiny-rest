<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use TinyRest\QueryBuilder\NativeQueryBuilder;
use TinyRest\Sort\SortHelper;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class NativeQueryProvider implements ProviderInterface
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

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return NativeQueryBuilder
     */
    public function provide(TransferObjectInterface $transferObject) : NativeQueryBuilder
    {
        $qb = $this->getQueryBuilder($transferObject);

        if ($transferObject instanceof SortableListTransferObjectInterface) {
            $this->applySort($qb, $transferObject);
        }

        return new NativeQueryBuilder($this->entityManager, $qb, $this->getRsm($transferObject));
    }

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return mixed
     */
    public function toArray(TransferObjectInterface $transferObject)
    {
        return $this->provide($transferObject)->createNativeQuery()->getResult();
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

    protected function applySort(QueryBuilder $queryBuilder, SortableListTransferObjectInterface $transferObject)
    {
        if (!SortHelper::isAllowedToSort($transferObject->getAllowedToSort(), $transferObject->getSort())) {
            return;
        }

        $field = SortHelper::getSortField($transferObject->getAllowedToSort(), $transferObject->getSort());
        $queryBuilder->addOrderBy($field, $transferObject->getSortDir());
    }
}

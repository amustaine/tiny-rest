<?php

namespace TinyRest\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use TinyRest\Sort\SortHelper;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
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

    abstract public function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder;

    /**
     * @param TransferObjectInterface|null $transferObject
     *
     * @return QueryBuilder
     */
    public function provide(TransferObjectInterface $transferObject) : QueryBuilder
    {
        $qb = $this->getQueryBuilder($transferObject);

        if ($transferObject instanceof SortableListTransferObjectInterface) {
            $this->applySort($qb, $transferObject);
        }

        return $qb;
    }

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

    protected function applySort(QueryBuilder $queryBuilder, SortableListTransferObjectInterface $transferObject)
    {
        if (!SortHelper::isAllowedToSort($transferObject->getAllowedToSort(), $transferObject->getSort())) {
            return;
        }

        $field = SortHelper::getSortField($transferObject->getAllowedToSort(), $transferObject->getSort());
        $queryBuilder->addOrderBy($field, SortHelper::getSortDir($transferObject->getSortDir()));
    }
}

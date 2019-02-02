<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use TinyRest\Sort\SortField;
use TinyRest\Sort\SortHelper;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\TransferObjectInterface;

abstract class ORMQueryBuilderProvider implements ProviderInterface
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

    public function provide(TransferObjectInterface $transferObject) : QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder($transferObject);

        if ($transferObject instanceof SortableListTransferObjectInterface) {
            $this->applySort($queryBuilder, $transferObject);
        }

        return $queryBuilder;
    }

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return mixed
     */
    public function toArray(TransferObjectInterface $transferObject)
    {
        return $this->provide($transferObject)->getQuery()->getResult();
    }

    public function createQueryBuilder() : QueryBuilder
    {
        return $this->entityManager->createQueryBuilder();
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

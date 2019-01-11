<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

    abstract public function getQueryBuilder() : QueryBuilder;

    public function getData(TransferObjectInterface $transferObject) : QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder();

        if ($transferObject instanceof SortableListTransferObjectInterface) {
            $this->applySort($queryBuilder, $transferObject);
        }

        return $queryBuilder;
    }
}

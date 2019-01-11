<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\TransferObjectInterface;

class EntityListProvider implements ProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $class;
    /**
     * @var array
     */
    private $sort;

    public function __construct(EntityManagerInterface $entityManager, string $class, array $sort = [])
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;
        $this->sort          = $sort;
    }

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return QueryBuilder
     */
    public function getData(TransferObjectInterface $transferObject) : QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('c')
            ->from($this->class, 'c');

        if ($this->sort) {
            $field = key($this->sort);
            $qb->orderBy('c.' . $field, $this->sort[$field]);
        }

        if ($transferObject instanceof SortableListTransferObjectInterface) {
            $this->applySort($qb, $transferObject);
        }

        return $qb;
    }

    protected function applySort(QueryBuilder $queryBuilder, SortableListTransferObjectInterface $transferObject)
    {
        if (in_array($transferObject->getSort(), $transferObject->getAllowedToSort())) {
            $queryBuilder->addOrderBy('c.' . $transferObject->getSort(), $transferObject->getDir());
        }
    }
}

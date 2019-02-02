<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use TinyRest\Sort\SortHelper;
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
    public function provide(TransferObjectInterface $transferObject) : QueryBuilder
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

    /**
     * @param TransferObjectInterface $transferObject
     *
     * @return mixed
     */
    public function toArray(TransferObjectInterface $transferObject)
    {
        return $this->provide($transferObject)->getQuery()->getResult();
    }

    protected function applySort(QueryBuilder $queryBuilder, SortableListTransferObjectInterface $transferObject)
    {
        if (!SortHelper::isAllowedToSort($transferObject->getAllowedToSort(), $transferObject->getSort())) {
            return;
        }

        $field = SortHelper::getSortField($transferObject->getAllowedToSort(), $transferObject->getSort());

        if (false === strpos($field, 'c.')) {
            $field = 'c.' . $field;
        }

        $queryBuilder->addOrderBy($field, $transferObject->getSortDir());
    }
}

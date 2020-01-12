<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class EntityListProvider implements ProviderInterface
{
    use SortTrait, FilterTrait;

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
    private $entitySort;

    public function __construct(EntityManagerInterface $entityManager, string $class, array $sort = [])
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;
        $this->entitySort    = $sort;
    }

    public function provide(): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('c')
            ->from($this->class, 'c');

        if ($this->entitySort) {
            $field = key($this->entitySort);
            $qb->orderBy('c.' . $field, $this->entitySort[$field]);
        }

        if ($this->sort && $this->sort->getField()) {
            $this->applySort($qb);
        }

        return $qb;
    }

    public function toArray(): array
    {
        return $this->provide()->getQuery()->getResult();
    }

    protected function applySort(QueryBuilder $queryBuilder)
    {
        $field = $this->sort->getField();

        if (false === strpos($field, 'c.')) {
            $field = 'c.' . $field;
        }

        $queryBuilder->addOrderBy($field, $this->sort->getSortDir());
    }
}

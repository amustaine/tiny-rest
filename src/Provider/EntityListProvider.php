<?php

namespace TinyRest\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class EntityListProvider implements ProviderInterface
{
    use SortTrait, FilterTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly string $class, private readonly array $entitySort = [])
    {

    }

    public function provide() : QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('c')
            ->from($this->class, 'c');

        if ($this->entitySort) {
            $field = key($this->entitySort);
            $qb->orderBy('c.' . $field, $this->entitySort[$field]);
        }

        if ($this->validSort()) {
            $this->applySort($qb);
        }

        return $qb;
    }

    public function toArray() : array
    {
        return $this->provide()->getQuery()->getResult();
    }

    protected function applySort(QueryBuilder $queryBuilder) : void
    {
        $field = $this->sort->getField();

        if (false === strpos($field, 'c.')) {
            $field = 'c.' . $field;
        }

        $queryBuilder->addOrderBy($field, $this->sort->getSortDir());
    }
}

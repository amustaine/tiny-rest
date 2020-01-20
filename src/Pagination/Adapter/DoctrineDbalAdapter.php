<?php

namespace TinyRest\Pagination\Adapter;

use Doctrine\DBAL\Query\QueryBuilder;

class DoctrineDbalAdapter extends \Pagerfanta\Adapter\DoctrineDbalAdapter
{
    public function __construct(QueryBuilder $queryBuilder)
    {
        parent::__construct($queryBuilder, $this->getQueryBuilderModifier());
    }

    /**
     * A default way to calculate total number of items in the query
     *
     * @return \Closure
     */
    protected function getQueryBuilderModifier() : \Closure
    {
        return function (QueryBuilder $queryBuilder) {
            $qb = clone $queryBuilder;

            $queryBuilder
                ->resetQueryParts()
                ->select('COUNT(*) as total_count')
                ->from("({$qb})", 'tmp');
        };
    }
}

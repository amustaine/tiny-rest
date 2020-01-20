<?php

namespace TinyRest\DataProvider;

use Doctrine\DBAL\Query\QueryBuilder;

class DoctrineDbalDataProvider extends AbstractDataProvider
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $this->sort($this->queryBuilder);
    }
}

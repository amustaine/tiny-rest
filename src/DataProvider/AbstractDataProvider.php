<?php

namespace TinyRest\DataProvider;

use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\QueryBuilder as OrmQueryBuilder;
use TinyRest\Model\SortModel;

abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var SortModel
     */
    private $sort;

    public function setSort(SortModel $sortModel)
    {
        $this->sort = $sortModel;
    }

    /**
     * @param DbalQueryBuilder|OrmQueryBuilder $queryBuilder
     */
    protected function sort($queryBuilder)
    {
        if ($this->sort && $this->sort->getField()) {
            $queryBuilder->addOrderBy($this->sort->getField(), $this->sort->getSortDir());
        }
    }
}

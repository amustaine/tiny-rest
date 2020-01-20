<?php

namespace TinyRest\DataProvider;

use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\Query as OrmQueryBuilder;
use TinyRest\Pagination\Adapter\DoctrineDbalAdapter;

class DataProviderFactory
{
    public static function createFromDbal(callable $callable, array $args = []): DoctrineDbalDataProvider
    {
        $qb = $callable(...$args);

        if (!$qb instanceof DbalQueryBuilder) {
            throw new \Exception('Expecting to get instance of "%s" from callable, "%s" given', DbalQueryBuilder::class, get_class($qb));
        }

        return new DoctrineDbalDataProvider($qb);
    }

    public static function createFromOrm(callable $callable, array $args = []): DoctrineDbalDataProvider
    {
        $qb = $callable(...$args);

        if (!$qb instanceof OrmQueryBuilder) {
            throw new \Exception('Expecting to get instance of "%s" from callable, "%s" given', OrmQueryBuilder::class, get_class($qb));
        }

        return new DoctrineDbalDataProvider($qb);
    }
}

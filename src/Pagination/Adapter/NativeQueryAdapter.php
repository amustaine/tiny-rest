<?php

namespace TinyRest\Pagination\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use TinyRest\QueryBuilder\NativeQueryBuilder;

class NativeQueryAdapter implements AdapterInterface
{
    public function __construct(private readonly NativeQueryBuilder $nativeQueryBuilder)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $qb = clone $this->nativeQueryBuilder->getQueryBuilder();
        $qb
            ->setMaxResults(null)
            ->setFirstResult(null);

        $sql = "SELECT COUNT(*) as cnt FROM ({$qb->getSQL()}) sub";

        return (int)$qb->getConnection()->executeQuery($sql, $qb->getParameters(), $qb->getParameterTypes())->fetchColumn();
    }

    public function getSlice($offset, $length)
    {
        $query = $this->nativeQueryBuilder
            ->setMaxResults($length)
            ->setFirstResult($offset)
            ->createNativeQuery();

        return $query->getResult();
    }
}

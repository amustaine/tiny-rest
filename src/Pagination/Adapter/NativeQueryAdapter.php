<?php

namespace TinyRest\Pagination\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use TinyRest\QueryBuilder\NativeQueryBuilder;

class NativeQueryAdapter implements AdapterInterface
{
    /**
     * @var NativeQueryBuilder
     */
    private $nativeQueryBuilder;

    public function __construct(NativeQueryBuilder $nativeQueryBuilder)
    {
        $this->nativeQueryBuilder = $nativeQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $qb  = $this->nativeQueryBuilder->getQueryBuilder();
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

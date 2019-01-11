<?php

namespace TinyRest\QueryBuilder;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;

class NativeQueryBuilder
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var ResultSetMapping
     */
    private $resultSetMapping;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        QueryBuilder $queryBuilder,
        ResultSetMapping $resultSetMapping
    )
    {
        $this->entityManager    = $entityManager;
        $this->queryBuilder     = $queryBuilder;
        $this->resultSetMapping = $resultSetMapping;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder() : QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setMaxResults(int $limit)
    {
        $this->queryBuilder->setMaxResults($limit);

        return $this;
    }

    public function setFirstResult(int $offset)
    {
        $this->queryBuilder->setFirstResult($offset);

        return $this;
    }

    public function createNativeQuery() : NativeQuery
    {
        $nativeQuery = $this->entityManager->createNativeQuery($this->queryBuilder->getSQL(), $this->resultSetMapping);

        foreach ($this->queryBuilder->getParameters() as $key => $value) {
            $nativeQuery->setParameter($key, $value, $this->queryBuilder->getParameterType($key));
        }

        return $nativeQuery;
    }
}

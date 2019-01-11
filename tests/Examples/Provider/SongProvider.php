<?php

namespace TinyRest\Tests\Examples\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use TinyRest\Provider\NativeQueryProvider;
use TinyRest\Tests\Examples\DTO\SongPaginatedListTransferObject;
use TinyRest\Tests\Examples\Entity\Song;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use TinyRest\TransferObject\TransferObjectInterface;

class SongProvider extends NativeQueryProvider
{
    /**
     * @param SongPaginatedListTransferObject $transferObject
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select('s.*')
            ->from('songs', 's')
            ->where($qb->expr()->like('s.name', ':name'))
            ->setParameter('name', "%{$transferObject->name}%");

        if ($transferObject->year) {
            $qb
                ->andWhere('s.year = :year')
                ->setParameter('year', $transferObject->year);
        }

        return $qb;
    }

    protected function getRsm(TransferObjectInterface $transferObject) : ResultSetMapping
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Song::class, 's');

        return $rsm;
    }
}

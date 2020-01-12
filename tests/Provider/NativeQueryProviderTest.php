<?php

namespace TinyRest\Tests\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use TinyRest\Model\SortModel;
use TinyRest\Provider\NativeQueryProvider;
use TinyRest\QueryBuilder\NativeQueryBuilder;
use TinyRest\Sort\SortField;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Tests\Examples\Entity\Song;
use TinyRest\TransferObject\TransferObjectInterface;

class NativeQueryProviderTest extends DatabaseTestCase
{
    public function testProvide()
    {
        $qb = $this->createProvider()->provide();

        $this->assertTrue($qb instanceof NativeQueryBuilder);
    }

    public function testToArray()
    {
        $class = $this->createProvider();
        $data  = $class->toArray();

        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertCount(4, $data);
        $this->assertTrue($data[0] instanceof Song);
    }

    public function testSort()
    {
        $provider = $this->createProvider();
        $provider->setSort(new SortModel('weight', 'desc', [new SortField('p.weight', 'weight')]));
        $qb = $provider->provide();
        $sort = $qb->getQueryBuilder()->getQueryPart('orderBy');

        $this->assertArrayHasKey(0, $sort);
        $this->assertEquals('p.weight desc', strtolower($sort[0]));
    }

    /**
     * @return NativeQueryProvider
     */
    private function createProvider() : NativeQueryProvider
    {
        return new class($this->getEntityManager()) extends NativeQueryProvider
        {
            public function getRsm(?TransferObjectInterface $transferObject) : ResultSetMapping
            {
                $rsm = new ResultSetMappingBuilder($this->getEntityManager());
                $rsm->addRootEntityFromClassMetadata(Song::class, 's');

                return $rsm;
            }

            public function getQueryBuilder(?TransferObjectInterface $transferObject) : QueryBuilder
            {
                $qb = $this->createQueryBuilder();
                $qb
                    ->select('s.*')
                    ->from('songs', 's');

                return $qb;
            }
        };
    }
}

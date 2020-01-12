<?php

namespace TinyRest\Tests\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use TinyRest\Model\SortModel;
use TinyRest\Sort\SortField;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Provider\DBALQueryBuilderProvider;
use TinyRest\TransferObject\TransferObjectInterface;

class DBALQueryBuilderProviderTest extends DatabaseTestCase
{
    public function testProvide()
    {
        $provider = $this->createProvider();
        $qb = $provider->provide();

        $this->assertTrue($qb instanceof QueryBuilder);
    }

    public function testToArray()
    {
        $provider = $this->createProvider();
        $data  = $provider->toArray();

        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertCount(4, $data);
        $this->assertTrue(is_array($data[0]));
    }

    public function testSort()
    {
        $provider = $this->createProvider();
        $provider->setSort(new SortModel('weight', 'desc', [new SortField('p.weight', 'weight')]));
        $qb = $provider->provide();
        $sort = $qb->getQueryPart('orderBy');

        $this->assertArrayHasKey(0, $sort);
        $this->assertEquals('p.weight desc', strtolower($sort[0]));
    }

    /**
     * @return DBALQueryBuilderProvider
     */
    private function createProvider() : DBALQueryBuilderProvider
    {
        return new class($this->getEntityManager()) extends DBALQueryBuilderProvider
        {
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

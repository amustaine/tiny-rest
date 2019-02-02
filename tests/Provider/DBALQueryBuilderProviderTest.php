<?php

namespace TinyRest\Tests\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Provider\DBALQueryBuilderProvider;
use TinyRest\Tests\Examples\DTO\ProductTransferObject;
use TinyRest\TransferObject\TransferObjectInterface;

class DBALQueryBuilderProviderTest extends DatabaseTestCase
{
    public function testProvide()
    {
        $qb = $this->createProvider()->provide(new ProductTransferObject());

        $this->assertTrue($qb instanceof QueryBuilder);
    }

    public function testToArray()
    {
        $class = $this->createProvider();
        $data  = $class->toArray(new ProductTransferObject());

        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertCount(4, $data);
        $this->assertTrue(is_array($data[0]));
    }

    public function testSort()
    {
        $transferObject = new ProductTransferObject();
        $transferObject->setSort('weight');
        $transferObject->setSortDir('desc');

        $qb = $this->createProvider()->provide($transferObject);
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
            public function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder
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

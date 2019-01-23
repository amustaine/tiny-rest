<?php

namespace TinyRest\Tests\Provider;

use Doctrine\DBAL\Query\QueryBuilder;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Provider\DBALQueryBuilderProvider;
use TinyRest\TransferObject\TransferObjectInterface;

class DBALQueryBuilderProviderTest extends DatabaseTestCase
{
    public function testProvide()
    {
        $qb = $this->createProvider()->provide($this->createTransferObject());

        $this->assertTrue($qb instanceof QueryBuilder);
    }

    public function testToArray()
    {
        $class = $this->createProvider();
        $data  = $class->toArray($this->createTransferObject());

        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertCount(4, $data);
        $this->assertTrue(is_array($data[0]));
    }

    /**
     * @return DBALQueryBuilderProvider
     */
    private function createProvider() : DBALQueryBuilderProvider
    {
        return new class($this->getEntityManager()) extends DBALQueryBuilderProvider
        {
            public function provide(TransferObjectInterface $transferObject) : QueryBuilder
            {
                $qb = $this->createQueryBuilder();
                $qb
                    ->select('s.*')
                    ->from('songs', 's');

                return $qb;
            }
        };
    }

    /**
     * @return TransferObjectInterface
     */
    private function createTransferObject() : TransferObjectInterface
    {
        return new class implements TransferObjectInterface
        {
        };
    }
}

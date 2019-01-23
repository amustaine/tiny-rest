<?php

namespace TinyRest\Tests\Provider;

use Doctrine\ORM\QueryBuilder;
use TinyRest\Provider\ORMQueryBuilderProvider;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Tests\Examples\Entity\Song;
use TinyRest\TransferObject\TransferObjectInterface;

class ORMQueryBuilderProviderTest extends DatabaseTestCase
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
        $this->assertTrue($data[0] instanceof Song);
    }

    /**
     * @return ORMQueryBuilderProvider
     */
    private function createProvider() : ORMQueryBuilderProvider
    {
        return new class($this->getEntityManager()) extends ORMQueryBuilderProvider
        {
            public function getQueryBuilder(TransferObjectInterface $transferObject) : QueryBuilder
            {
                $qb = $this->createQueryBuilder();
                $qb
                    ->select('s')
                    ->from(Song::class, 's');

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

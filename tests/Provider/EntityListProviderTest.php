<?php

namespace TinyRest\Tests\Provider;

use Doctrine\ORM\QueryBuilder;
use TinyRest\Provider\EntityListProvider;
use TinyRest\Sort\SortField;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Tests\Examples\DTO\ProductTransferObject;
use TinyRest\Tests\Examples\Entity\Song;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\SortTrait;
use TinyRest\TransferObject\TransferObjectInterface;

class EntityListProviderTest extends DatabaseTestCase
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

    public function testSort()
    {
        $transferObject = $this->createTransferObject();
        $transferObject->setSort('name');
        $transferObject->setSortDir('desc');

        $qb = $this->createProvider()->provide($transferObject);
        $sort = $qb->getDQLPart('orderBy');

        $sortVal = $sort[0];

        $this->assertArrayHasKey(0, $sort);
        $this->assertEquals('c.name desc', strtolower($sortVal->getParts()[0]));
    }


    /**
     * @return EntityListProvider
     */
    private function createProvider() : EntityListProvider
    {
        return new class($this->getEntityManager(), Song::class) extends EntityListProvider
        {
        };
    }

    /**
     * @return TransferObjectInterface
     */
    private function createTransferObject() : TransferObjectInterface
    {
        return new class implements TransferObjectInterface, SortableListTransferObjectInterface
        {
            use SortTrait;

            public function getAllowedToSort() : array
            {
                return [
                    new SortField('c.name', 'name')
                ];
            }
        };
    }
}

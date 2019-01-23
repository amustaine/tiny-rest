<?php

namespace TinyRest\Tests\Provider;

use Doctrine\ORM\QueryBuilder;
use TinyRest\Provider\EntityListProvider;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Tests\Examples\Entity\Song;
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
        return new class implements TransferObjectInterface
        {
        };
    }
}

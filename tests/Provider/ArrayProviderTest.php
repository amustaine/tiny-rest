<?php

namespace TinyRest\Tests\Provider;

use TinyRest\Provider\ArrayProvider;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\TransferObject\TransferObjectInterface;

class ArrayProviderTest extends DatabaseTestCase
{
    public function testProvide()
    {
        $qb = $this->createProvider()->provide($this->createTransferObject());

        $this->assertTrue(is_array($qb));
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
     * @return ArrayProvider
     */
    private function createProvider() : ArrayProvider
    {
        return new class extends ArrayProvider
        {
            public function provide(): array
            {
                return [
                    ['id' => 1],
                    ['id' => 2],
                    ['id' => 3],
                    ['id' => 4]
                ];
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

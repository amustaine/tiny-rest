<?php

namespace TinyRest\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use TinyRest\Pagination\TransferObjectFactory;
use TinyRest\TransferObject\TransferObjectInterface;

class TransferObjectFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new TransferObjectFactory();
        $factory
            ->addSortField('foo', 'bar')
            ->addSortField('baz', 'a', 'b', 'c', 'd');

        $transferObject = $factory->create();

        $this->assertInstanceOf(TransferObjectInterface::class, $transferObject);
        $this->assertEquals(2, count($transferObject->getAllowedToSort()));
    }
}

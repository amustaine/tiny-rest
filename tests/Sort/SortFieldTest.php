<?php

namespace TinyRest\Sort;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SortFieldTest extends KernelTestCase
{
    public function testGetFieldByAliasWithOneAliases()
    {
        $sortField = new SortField('c.name', 'c_name');

        $this->assertNull($sortField->getFieldByAlias('c.name'));
        $this->assertEquals('c.name', $sortField->getFieldByAlias('c_name'));
    }

    public function testGetFieldByAliasWithMultipleAliases()
    {
        $sortField = new SortField('c.name', 'c_name', 'b', 'c');

        $this->assertNull($sortField->getFieldByAlias('c.name'));
        $this->assertEquals('c.name', $sortField->getFieldByAlias('c_name'));
        $this->assertEquals('c.name', $sortField->getFieldByAlias('b'));
        $this->assertEquals('c.name', $sortField->getFieldByAlias('c'));
    }
}

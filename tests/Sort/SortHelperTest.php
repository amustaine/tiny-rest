<?php

namespace TinyRest\Tests\Sort;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TinyRest\Sort\SortField;
use TinyRest\Sort\SortHelper;

class SortHelperTest extends KernelTestCase
{
    /**
     * @param string $sort
     * @param bool $result
     *
     * @dataProvider sortAllowedProvider
     */
    public function testPositiveIsAllowedToSort(string $sort, bool $result)
    {
        $sortFields = [
            'id',
            'title',
            new SortField('c.name', 'c_name'),
            new SortField('b.data', 'a', 'b', 'c', 'd', 'e')
        ];

        $this->assertEquals($result, SortHelper::isAllowedToSort($sortFields, $sort));
    }

    public function sortAllowedProvider()
    {
        return [
            ['id', true],
            ['title', true],
            ['c_name', true],
            ['a', true],
            ['d', true],
            ['f', false],
            ['c.name', false]
        ];
    }

    /**
     * @param string $sort
     * @param string $result
     *
     * @dataProvider sortFieldProvider
     */
    public function testGetSortField(string $sort, string $result)
    {
        $sortFields = [
            'id',
            'title',
            new SortField('c.name', 'c_name'),
            new SortField('b.data', 'a', 'b', 'c', 'd', 'e')
        ];

        $this->assertEquals($result, SortHelper::getSortField($sortFields, $sort));
    }

    public function sortFieldProvider()
    {
        return [
            ['id', 'id'],
            ['title', 'title'],
            ['c_name', 'c.name'],
            ['b', 'b.data'],
            ['e', 'b.data'],
        ];
    }

    public function testNegativeSort()
    {
        $sort = 'foo';

        $sortFields = [
            'n' . $sort,
        ];
        $this->assertEquals('foo', SortHelper::getSortField($sortFields, $sort));
    }
}

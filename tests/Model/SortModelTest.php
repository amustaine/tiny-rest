<?php

namespace TinyRest\Tests\Model;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use TinyRest\Model\SortModel;

class SortModelTest extends TestCase
{
    public function testCreateFromRequest()
    {
        $request = Request::create('localhost', 'GET', ['sort' => 'id', 'sortDir' => 'desc']);
        $model = SortModel::createFromRequest($request, ['id' => 'id']);

        $this->assertEquals('id', $model->getSort());
        $this->assertEquals('desc', $model->getSortDir());
    }

    public function testDefaults()
    {
        $request = Request::create('localhost', 'GET', []);
        $model = SortModel::createFromRequest($request, []);

        $this->assertEquals(null, $model->getSort());
        $this->assertEquals('asc', $model->getSortDir());
    }

    public function testInvalidSortDir()
    {
        $model = new SortModel('id', 'foo', ['id' => 'id']);
        $this->assertEquals('id', $model->getSort());
        $this->assertEquals('asc', $model->getSortDir());
    }
}

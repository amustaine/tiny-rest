<?php

namespace TinyRest\Tests\Model;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use TinyRest\Model\PaginationModel;

class PaginationModelTest extends TestCase
{
    public function testCreateFromRequest()
    {
        $request = Request::create('localhost', 'GET', ['page' => 12, 'pageSize' => 20]);

        $model = PaginationModel::createFromRequest($request);
        $this->assertEquals(12, $model->getPage());
        $this->assertEquals(20, $model->getPageSize());
    }

    public function testEmptyRequestData()
    {
        $request = Request::create('localhost', 'GET', []);

        $model = PaginationModel::createFromRequest($request);
        $this->assertEquals(1, $model->getPage());
        $this->assertEquals(20, $model->getPageSize());
    }

    public function testEmptyStringData()
    {
        $request = Request::create('localhost', 'GET', ['page' => '', 'pageSize' => '']);

        $model = PaginationModel::createFromRequest($request);
        $this->assertEquals(1, $model->getPage());
        $this->assertEquals(20, $model->getPageSize());
    }

    public function testNonNumericPage()
    {
        $request = Request::create('localhost', 'GET', ['page' => "HELLO WORLD!"]);

        $model = PaginationModel::createFromRequest($request);
        $this->assertEquals(1, $model->getPage());
    }

    public function testNegativePage()
    {
        $model = new PaginationModel(-1, null);
        $this->assertEquals(1, $model->getPage());
    }

    public function testNegativePageSize()
    {
        $model = new PaginationModel(1, -1);
        $this->assertEquals(1, $model->getPage());
        $this->assertEquals(-1, $model->getPageSize());
    }

    public function testEnvPageSize()
    {
        putenv('TINYREST_PAGE_SIZE=15');
        $model = new PaginationModel(1, null);
        $this->assertEquals(1, $model->getPage());
        $this->assertEquals(15, $model->getPageSize());
    }
}

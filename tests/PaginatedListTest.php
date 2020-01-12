<?php

namespace TinyRest\Tests;

use Symfony\Component\HttpFoundation\Request;
use TinyRest\Exception\ValidationException;
use TinyRest\Tests\Examples\Entity\TestItem;
use TinyRest\Tests\Examples\Provider\SongProvider;
use TinyRest\Tests\Examples\DTO\SongPaginatedListTransferObject;
use TinyRest\Tests\Examples\Entity\Song;

class PaginatedListTest extends RequestHandlerCase
{
    public function testNativeQueryPaginatedList()
    {
        $request          = Request::create('localhost', 'GET', ['page' => 1, 'name' => 'Larger than life']);
        $transferObject   = new SongPaginatedListTransferObject();

        $handler    = $this->createRequestHandler();
        $collection = $handler->getPaginatedList(
            $request,
            $transferObject,
            $handler->getProviderFactory()->createNativeQueryProvider(SongProvider::class)
        );

        $this->assertEquals(1, $collection->getTotal());
        $this->assertEquals(Song::class, get_class($collection->getData()[0]));
    }

    public function testEntityPaginatedList()
    {
        $request        = Request::create('localhost', 'GET', ['page' => 2, 'pageSize' => 2]);
        $transferObject = new SongPaginatedListTransferObject();

        $handler    = $this->createRequestHandler();
        $collection = $handler->getPaginatedList(
            $request,
            $transferObject,
            $handler->getProviderFactory()->createEntityListProvider(Song::class)
        );

        $this->assertEquals(4, $collection->getTotal());
        $this->assertEquals(2, count($collection->getData()));
        $this->assertEquals(2, $collection->getPage());
        $this->assertEquals(Song::class, get_class($collection->getData()[0]));
    }

    public function testSort()
    {
        $request        = Request::create('localhost', 'GET', ['page' => 2, 'pageSize' => 2, 'sort' => 'id', 'sortDir' => 'desc']);
        $transferObject = new SongPaginatedListTransferObject();

        $handler    = $this->createRequestHandler();
        $collection = $handler->getPaginatedList(
            $request,
            $transferObject,
            $handler->getProviderFactory()->createEntityListProvider(Song::class, ['id' => 'desc'])
        );

        $this->assertEquals(2, $collection->getData()[0]->getId());
    }

    public function testNegativePage()
    {
        $request        = Request::create('localhost', 'GET', ['page' => -1, 'pageSize' => 2, 'sort' => 'id', 'sortDir' => 'desc']);
        $transferObject = new SongPaginatedListTransferObject();

        $handler    = $this->createRequestHandler();

        $this->expectException(ValidationException::class);
        $handler->getPaginatedList(
            $request,
            $transferObject,
            $handler->getProviderFactory()->createEntityListProvider(Song::class, ['id' => 'desc'])
        );
    }

    public function testNegativePageSize()
    {
        $request        = Request::create('localhost', 'GET', ['page' => 1, 'pageSize' => -20, 'sort' => 'id', 'sortDir' => 'desc']);
        $transferObject = new SongPaginatedListTransferObject();

        $handler    = $this->createRequestHandler();

        $this->expectException(ValidationException::class);
        $handler->getPaginatedList(
            $request,
            $transferObject,
            $handler->getProviderFactory()->createEntityListProvider(Song::class, ['id' => 'desc'])
        );
    }

    public function testWithoutPaginatedInterface()
    {
        $request = Request::create(
            'localhost',
            'GET',
            ['page' => 2, 'pageSize' => 5]
        );
        $handler = $this->createRequestHandler();

        $collection = $handler->getPaginatedList(
            $request,
            null,
            $handler->getProviderFactory()->createEntityListProvider(TestItem::class)
        );

        $this->assertEquals(2, $collection->getPage());
        $this->assertEquals(5, $collection->getPerPage());
        $this->assertEquals('TestItem#6', $collection->getData()[0]->getName());
    }
}

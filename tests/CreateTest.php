<?php

namespace TinyRest\Tests;

use TinyRest\Tests\Examples\DTO\AlbumTransferObject;
use TinyRest\Tests\Examples\DTO\ArtistCreateTransferObject;
use TinyRest\Tests\Examples\Entity\Album;
use TinyRest\Tests\Examples\Entity\Artist;
use Symfony\Component\HttpFoundation\Request;

class CreateTest extends RequestHandlerCase
{
    public function testCreate()
    {
        $jsonData = [
            'genre' => 'Rave',
            'name'  => 'Scooter',
            'year'  => 1990
        ];
        $request  = Request::create('localhost', 'POST', [], [], [], [], json_encode($jsonData));

        $handler = $this->createRequestHandler();
        $artist  = $handler->create($request, $dto = new ArtistCreateTransferObject(), new Artist());

        $this->assertEquals('Rave', $dto->genre);
        $this->assertEquals('Scooter', $dto->name);
        $this->assertEquals('Scooter', $artist->getName());
        $this->assertEquals('Rave', $artist->getGenre());
    }

    public function testWithRelations()
    {
        $jsonData = [
            'name'   => 'Unbreakable',
            'year'   => 2007,
            'artist' => 'Backstreet Boys',
            'cover'  => 2
        ];

        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode($jsonData));

        $handler = $this->createRequestHandler();
        $album   = $handler->create($request, $dto = new AlbumTransferObject(), new Album());

        $this->assertEquals('Backstreet Boys', $dto->artist);
        $this->assertEquals('Backstreet Boys', $album->getArtist()->getName());
        $this->assertEquals('https://picsum.photos/200/300?image=2', $album->getCover()->getUrl());
    }
}

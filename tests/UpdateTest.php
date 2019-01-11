<?php

namespace TinyRest\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use TinyRest\RequestHandler;
use TinyRest\Tests\Examples\DTO\ArtistUpdateTransferObject;
use TinyRest\Tests\Examples\Entity\Artist;

class UpdateTest extends RequestHandlerCase
{
    public function testUpdateWithPut()
    {
        $artist = new Artist();
        $artist
            ->setYear(1990)
            ->setGenre('Folk');

        $jsonData = [
            'year' => 2001,
        ];
        $request = Request::create('localhost', 'PUT', [], [], [], [], json_encode($jsonData));

        $dto = new ArtistUpdateTransferObject();
        $handler = $this->createRequestHandler();
        $handler->update($request, $dto, $artist);

        $this->assertEquals(2001, $dto->year);
        $this->assertEquals(2001, $artist->getYear());
        $this->assertNull($artist->getGenre());
    }

    public function testUpdateWithPatch()
    {
        $artist = new Artist();
        $artist
            ->setYear(1990)
            ->setGenre('Folk');

        $jsonData = [
            'year' => 2001,
        ];
        $request = Request::create('localhost', 'PATCH', [], [], [], [], json_encode($jsonData));

        $dto = new ArtistUpdateTransferObject();
        $handler = $this->createRequestHandler();
        $handler->update($request, $dto, $artist);

        $this->assertEquals(2001, $dto->year);
        $this->assertEquals(2001, $artist->getYear());
        $this->assertEquals('Folk', $artist->getGenre());
    }
}

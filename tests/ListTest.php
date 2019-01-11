<?php

namespace TinyRest\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use TinyRest\Tests\Examples\DTO\ArtistListTransferObject;


class ListTest extends TestCase
{
    public function testHandleRequest()
    {
        $request = Request::create('localhost', 'GET', ['genre' => 'rock', 'artist_name' => 'Pink Floyd', 'year' => '1985']);

        $dto = new ArtistListTransferObject();
        $dto->handleRequest($request);

        $this->assertEquals('rock', $dto->genre);
        $this->assertEquals('Pink Floyd', $dto->artistName);
        $this->assertEquals(1985, $dto->year);
    }

    public function testDtoValidation()
    {
        $request = Request::create('localhost', 'GET', ['artist_name' => 'Pink Floyd', 'year' => '1985']);

        $dto = new ArtistListTransferObject();
        $dto->handleRequest($request);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $violationsList = $validator->validate($dto);

        $this->assertTrue(count($violationsList) > 0);
    }
}

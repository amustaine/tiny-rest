<?php

namespace TinyRest\Tests;

use TinyRest\Hydrator\EntityHydrator;
use TinyRest\Tests\Examples\DTO\AlbumTransferObject;
use TinyRest\Tests\Examples\DTO\UserTransferObject;
use TinyRest\Tests\Examples\Entity\Album;
use TinyRest\Tests\Examples\Entity\Artist;
use TinyRest\Tests\Examples\Entity\Cover;
use TinyRest\Tests\Examples\Entity\User;

class EntityHydratorTest extends RequestHandlerCase
{
    public function testSimple()
    {
        $transferObject            = new UserTransferObject();
        $transferObject->firstName = 'John';
        $transferObject->lastName  = 'Doe';

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $user = new User();
        $entityHydrator->hydrate($transferObject, $user);

        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
    }

    public function testWithClearFields()
    {
        $transferObject            = new UserTransferObject();
        $transferObject->firstName = 'John';
        $transferObject->lastName  = 'Doe';

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $user = (new User())
            ->setEmail('test@test.com');
        $entityHydrator->hydrate($transferObject, $user, true);

        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertNull($user->getEmail());
    }

    public function testCustomColumnName()
    {
        $transferObject            = new UserTransferObject();
        $transferObject->lifeStyle = 'Actor';

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $user = new User();
        $entityHydrator->hydrate($transferObject, $user);

        $this->assertEquals('Actor', $user->getHobby());
    }

    public function testUnmappedColumn()
    {
        $transferObject        = new UserTransferObject();
        $transferObject->email = 'test@test.com';

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $user = new User();
        $entityHydrator->hydrate($transferObject, $user);

        $this->assertEmpty($user->getEmail());
    }

    public function testRelations()
    {
        $transferObject         = new AlbumTransferObject();
        $transferObject->artist = 'Backstreet Boys';
        $transferObject->cover  = 2;

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $album = new Album();
        $entityHydrator->hydrate($transferObject, $album);

        $this->assertEquals(Artist::class, get_class($album->getArtist()));
        $this->assertEquals('Backstreet Boys', $album->getArtist()->getName());
        $this->assertEquals(Cover::class, get_class($album->getCover()));
        $this->assertEquals(2, $album->getCover()->getId());
    }

    public function testDates()
    {
        $transferObject            = new UserTransferObject();
        $transferObject->birthDate = '10/16/1988';

        $entityHydrator = new EntityHydrator($this->getEntityManager());

        $user = new User();
        $entityHydrator->hydrate($transferObject, $user);

        $this->assertEquals(\DateTime::class, get_class($user->getBirthDate()));
        $this->assertEquals('16101988', $user->getBirthDate()->format('dmY'));
    }
}

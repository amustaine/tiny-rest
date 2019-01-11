<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\Album;
use TinyRest\Tests\Examples\Entity\Artist;
use TinyRest\Tests\Examples\Entity\Cover;

class AlbumFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $artist = $manager->getRepository(Artist::class)->findOneBy(['name' => 'Backstreet Boys']);
        $cover  = $manager->getRepository(Cover::class)->findOneBy([]);

        $album = new Album();
        $album
            ->setName('Millennium')
            ->setYear(1999)
            ->setArtist($artist)
            ->setCover($cover);

        $manager->persist($album);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}

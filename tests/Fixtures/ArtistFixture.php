<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\Album;
use TinyRest\Tests\Examples\Entity\Artist;
use TinyRest\Tests\Examples\Entity\Song;

class ArtistFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $artist = new Artist();
        $artist
            ->setName('Backstreet Boys')
            ->setYear(1996)
            ->setGenre('Pop');

        $manager->persist($artist);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}

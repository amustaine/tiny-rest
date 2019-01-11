<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\Album;
use TinyRest\Tests\Examples\Entity\Song;

class SongFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $album = $manager->getRepository(Album::class)->findOneBy(['name' => 'Millennium']);

        $song = new Song();
        $song
            ->setName('Larger than life')
            ->setAlbum($album);
        $manager->persist($song);

        $song = new Song();
        $song
            ->setName('I want it that way')
            ->setAlbum($album);
        $manager->persist($song);

        $song = new Song();
        $song
            ->setName('Show me the meaning of being lonely')
            ->setAlbum($album);
        $manager->persist($song);

        $song = new Song();
        $song
            ->setName('It\'s gotta be you')
            ->setAlbum($album);
        $manager->persist($song);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}

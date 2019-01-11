<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\Cover;

class CoverFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $cover = new Cover();
        $cover
            ->setUrl('https://picsum.photos/200/300?image=1');
        $manager->persist($cover);

        $cover = new Cover();
        $cover
            ->setUrl('https://picsum.photos/200/300?image=2');
        $manager->persist($cover);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}

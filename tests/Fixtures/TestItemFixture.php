<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\Cover;
use TinyRest\Tests\Examples\Entity\TestItem;

class TestItemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $obj = (new TestItem())->setName('TestItem#'.($i+1));

            $manager->persist($obj);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}

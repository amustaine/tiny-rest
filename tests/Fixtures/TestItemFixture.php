<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\TestItem;
use TinyRest\Tests\Examples\Entity\User;

class TestItemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 25; $i++) {
            $obj = (new TestItem())
                ->setName('TestItem#'.$i)
                ->setAdditionalName('Additional#'.$i)
                ->setUser($manager->getRepository(User::class)->find(ceil($i / 5)))
            ;

            $manager->persist($obj);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}

<?php

namespace TinyRest\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TinyRest\Tests\Examples\Entity\User;

class UserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $obj = (new User())
                ->setUsername('TestUser#'.($i+1))
                ->setFirstName('FirstName#'.($i+1))
                ->setLastName('LastName#'.($i+1))
                ->setEmail('LastName#'.($i+1).'@local.local')
                ->setHobby('')
                ->setBirthDate(new \DateTime())
            ;

            $manager->persist($obj);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}

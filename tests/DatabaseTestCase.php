<?php

namespace TinyRest\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseTestCase extends KernelTestCase
{
    public function getEntityManager()
    {
        $annotationDriver = new AnnotationDriver(new IndexedReader(new AnnotationReader()));
        $annotationDriver->addPaths([__DIR__ . '/Examples/Entity']);

        $config = new Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('TinyRest\Tests\Examples\Entity');
        $config->setMetadataDriverImpl($annotationDriver);
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());

        $params = [
            'driver' => 'pdo_sqlite',
            'path'   => 'database.sqlite',
            'memory' => true,
        ];

        return EntityManager::create($params, $config);
    }

    public function setUp(): void
    {
        $em = $this->getEntityManager();

        $meta = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($meta);
        $schemaTool->createSchema($meta);

        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/Fixtures');

        $executor = new ORMExecutor($this->getEntityManager(), new ORMPurger());
        $executor->execute($loader->getFixtures());
    }
}

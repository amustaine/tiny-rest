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
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\RequestHandler;

class RequestHandlerCase extends TestCase
{
    /**
     * @return RequestHandler
     */
    protected function createRequestHandler() : RequestHandler
    {
        return new RequestHandler($this->getEntityManager(), $this->getValidator(), $this->getPaginationFactory());
    }

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

    public function setUp()
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

    /**
     * @return ValidatorInterface
     */
    protected function getValidator() : ValidatorInterface
    {
        return Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    /**
     * @return PaginationFactory
     */
    protected function getPaginationFactory() : PaginationFactory
    {
        return new PaginationFactory($this->getRouter(), $this->getRequestStack());
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter() : RouterInterface
    {
        return $this->createMock(RouterInterface::class);
    }

    /**
     * @return RequestStack
     */
    protected function getRequestStack() : RequestStack
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn(Request::createFromGlobals());

        return $requestStack;
    }
}

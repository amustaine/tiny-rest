<?php

namespace TinyRest\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\RequestHandler;
use TinyRest\Serializer\Serializer;

class RequestHandlerCase extends DatabaseTestCase
{
    /**
     * @return RequestHandler
     */
    protected function createRequestHandler() : RequestHandler
    {
        return new RequestHandler($this->getEntityManager(), $this->getValidator(), $this->getPaginationFactory(), $this->getSerializer());
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
        $request = Request::createFromGlobals();
        $request->attributes->set('_route', 'mockhost');

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        return $requestStack;
    }

    protected function getSerializer(): SerializerInterface
    {
        return Serializer::create();
    }
}

<?php

namespace TinyRest\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\RequestHandler;

class RequestHandlerCase extends DatabaseTestCase
{
    /**
     * @return RequestHandler
     */
    protected function createRequestHandler() : RequestHandler
    {
        return new RequestHandler($this->getEntityManager(), $this->getValidator(), $this->getPaginationFactory());
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

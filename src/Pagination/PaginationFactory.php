<?php

namespace TinyRest\Pagination;

use TinyRest\Provider\ProviderInterface;
use TinyRest\Pagination\Adapter\NativeQueryAdapter;
use TinyRest\QueryBuilder\NativeQueryBuilder;
use TinyRest\TransferObject\PaginatedListTransferObjectInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router       = $router;
        $this->requestStack = $requestStack;
    }

    public function createCollection(PaginatedListTransferObjectInterface $transferObject, ProviderInterface $dataProvider) : PaginatedCollection
    {
        try {
            $adapter    = $this->getAdapter($dataProvider->getData($transferObject));
            $pagerfanta = new Pagerfanta($adapter);

            $pagerfanta
                ->setMaxPerPage($transferObject->getPageSize())
                ->setCurrentPage($transferObject->getPage());

            $pagerfanta->getIterator();
        } catch (OutOfRangeCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return new PaginatedCollection($pagerfanta, $this->getRouteBuilder());
    }

    /**
     * @param $dataProvider
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getAdapter($dataProvider) : AdapterInterface
    {
        if ($dataProvider instanceof QueryBuilder) {
            $adapter = new DoctrineDbalAdapter($dataProvider, $this->getQueryBuilderModifier());
        } elseif ($dataProvider instanceof \Doctrine\ORM\QueryBuilder) {
            $adapter = new DoctrineORMAdapter($dataProvider);
        } elseif (is_array($dataProvider)) {
            $adapter = new ArrayAdapter($dataProvider);
        } elseif ($dataProvider instanceof NativeQueryBuilder) {
            $adapter = new NativeQueryAdapter($dataProvider);
        }

        if (empty($adapter)) {
            throw new \Exception(sprintf("Unsupportable provider given: %s", get_class($dataProvider)));
        }

        return $adapter;
    }

    /**
     * A default way to calculate total number of items in the query
     *
     * @return \Closure
     */
    private function getQueryBuilderModifier() : \Closure
    {
        return function (QueryBuilder $queryBuilder) {
            $qb = clone $queryBuilder;

            $queryBuilder
                ->resetQueryParts()
                ->select('COUNT(*) as total_count')
                ->from("({$qb})", 'tmp');
        };
    }

    private function getRouteBuilder()
    {
        $request     = $this->requestStack->getCurrentRequest();
        $routeName   = $request->get('_route');
        $routeParams = $request->query->all();
        unset($routeParams['page']);

        $routeBuilder = function ($pageNumber) use ($routeName, $routeParams) {
            $routeParams['page'] = $pageNumber;

            return $this->router->generate($routeName, $routeParams);
        };

        return $routeBuilder;
    }
}

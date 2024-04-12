<?php

namespace TinyRest\Pagination;

use TinyRest\Model\PaginationModel;
use TinyRest\Provider\ProviderInterface;
use TinyRest\Pagination\Adapter\NativeQueryAdapter;
use TinyRest\QueryBuilder\NativeQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\DBAL\QueryAdapter as DoctrineDbalAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter as DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    public function __construct(private readonly RouterInterface $router, private readonly RequestStack $requestStack)
    {

    }

    public function createCollection(PaginationModel $paginationModel, ProviderInterface $dataProvider) : PaginatedCollection
    {
        try {
            $adapter    = $this->getAdapter($dataProvider->provide());
            $pagerfanta = new Pagerfanta($adapter);

            $pagerfanta
                ->setMaxPerPage($paginationModel->getPageSize())
                ->setCurrentPage($paginationModel->getPage());

            $pagerfanta->getIterator();
        } catch (OutOfRangeCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return new PaginatedCollection($pagerfanta, $this->getRouteBuilder());
    }

    /**
     * @return PaginatedCollection
     */
    public function createEmptyCollection() : PaginatedCollection
    {
        return new PaginatedCollection(new Pagerfanta(new ArrayAdapter([])), $this->getRouteBuilder());
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

    private function getRouteBuilder() : ?callable
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $routeName   = $request->get('_route');
        $queryParams = $request->query->all();
        $routeParams = array_merge($request->attributes->get('_route_params') ?? [], $queryParams);
        unset($routeParams['page']);

        $routeBuilder = function ($pageNumber) use ($routeName, $routeParams) {
            $routeParams['page'] = $pageNumber;

            return $this->router->generate($routeName, $routeParams);
        };

        return $routeBuilder;
    }
}

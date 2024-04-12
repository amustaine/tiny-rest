<?php

namespace TinyRest\Pagination;

use TinyRest\Pagination\Normalizer\PaginationNormalizerInterface;
use Pagerfanta\Pagerfanta;

class PaginatedCollection
{
    private int $total;
    private int $page;
    private int $perPage;

    protected ?PaginationNormalizerInterface $normalizer = null;

    private $routeBuilder;

    public function __construct(private readonly Pagerfanta $pagerfanta, ?callable $routeBuilder)
    {
        $this->total        = $this->pagerfanta->getNbResults();
        $this->page         = $this->pagerfanta->getCurrentPage();
        $this->perPage      = $this->pagerfanta->getMaxPerPage();
        $this->routeBuilder = $routeBuilder;
    }

    public function setNormalizer(PaginationNormalizerInterface $normalizer) : void
    {
        $this->normalizer = $normalizer;
    }

    public function getData() : array
    {
        $data = [];

        foreach ($this->pagerfanta->getIterator() as $row) {
            if ($this->normalizer) {
                $row = $this->normalizer->normalize($row);
            }

            $data[] = $row;
        }

        return $data;
    }

    public function getTotal() : int
    {
        return $this->total;
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function getPerPage() : int
    {
        return $this->perPage;
    }

    public function getLinks()
    {
        $routeBuilder = $this->routeBuilder;

        if (null === $routeBuilder) {
            return [];
        }

        $links = [
            'self'  => $routeBuilder($this->pagerfanta->getCurrentPage()),
            'first' => $routeBuilder(1),
            'last'  => $routeBuilder($this->pagerfanta->getNbPages())
        ];

        if ($this->pagerfanta->hasPreviousPage()) {
            $links['prev'] = $routeBuilder($this->pagerfanta->getPreviousPage());
        }

        if ($this->pagerfanta->hasNextPage()) {
            $links['next'] = $routeBuilder($this->pagerfanta->getNextPage());
        }

        return $links;
    }

    /**
     * @return array
     */
    public function normalize() : array
    {
        return [
            'total'   => $this->getTotal(),
            'perPage' => $this->getPerPage(),
            'data'    => $this->getData(),
            'links'   => $this->getLinks()
        ];
    }
}

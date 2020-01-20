<?php

namespace TinyRest\Pagination;

use TinyRest\Pagination\Normalizer\PaginationNormalizerInterface;
use Pagerfanta\Pagerfanta;

class PaginatedCollection
{
    /**
     * @var int
     */
    private $total;

    /**
     * @var Pagerfanta
     */
    private $pagerfanta;

    /**
     * @var PaginationNormalizerInterface|null $normalizer
     */
    protected $normalizer;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var callable
     */
    private $routeBuilder;

    public function __construct(Pagerfanta $pagerfanta, callable $routeBuilder)
    {
        $this->pagerfanta   = $pagerfanta;
        $this->total        = $this->pagerfanta->getNbResults();
        $this->page         = $this->pagerfanta->getCurrentPage();
        $this->perPage      = $this->pagerfanta->getMaxPerPage();
        $this->routeBuilder = $routeBuilder;
    }

    public function setNormalizer(PaginationNormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @return array
     */
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

    /**
     * @return int
     */
    public function getTotal() : int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getPage() : int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage() : int
    {
        return $this->perPage;
    }

    public function getLinks()
    {
        $routeBuilder = $this->routeBuilder;

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

    public function normalize(): array
    {
        return [
            'total'    => $this->getTotal(),
            'perPage'  => $this->getPerPage(), //legacy field, will be removed in 2.0
            'pageSize' => $this->getPerPage(),
            'data'     => $this->getData(),
            'links'    => $this->getLinks(),
        ];
    }
}

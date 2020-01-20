<?php

namespace TinyRest\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use TinyRest\Annotations\ResourceReader;
use TinyRest\Model\PaginationModel;
use TinyRest\Model\SortModel;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\Provider\EntityListProvider;

class ApiResourceHandler
{
    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, PaginationFactory $paginationFactory, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->paginationFactory = $paginationFactory;
        $this->serializer = $serializer;
    }

    public function handle(Request $request, string $apiResource)
    {
        $resourceReader = new ResourceReader($apiResource);
        $provider       = new EntityListProvider($this->entityManager, $apiResource);

        $pagination = $this->getPagination($request);
        $sort       = $this->getSort($request, $resourceReader->getSortFields());

        $provider->setSort($sort);

        $collection = $this->paginationFactory->createCollection($pagination, $provider);

        return $this->serializer->serialize($collection->normalize(), 'json', $resourceReader->getNormalizationContext());
    }

    protected function getPagination(Request $request): PaginationModel
    {
        return PaginationModel::createFromRequest($request);
    }

    protected function getSort(Request $request, array $sortFields): SortModel
    {
        return SortModel::createFromRequest($request, $sortFields);
    }
}

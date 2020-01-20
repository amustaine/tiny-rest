<?php

namespace TinyRest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Annotations\ResourceReader;
use TinyRest\DataProvider\DataProviderInterface;
use TinyRest\Handler\ApiResourceHandler;
use TinyRest\Model\PaginationModel;
use TinyRest\Model\SortModel;
use TinyRest\Provider\EntityListProvider;
use TinyRest\Provider\ProviderInterface;
use TinyRest\ListCollection\Collection;
use TinyRest\Exception\ValidationException;
use TinyRest\Hydrator\EntityHydrator;
use TinyRest\Hydrator\TransferObjectHydrator;
use TinyRest\Pagination\PaginatedCollection;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\Provider\ProviderFactory;
use TinyRest\Serializer\Serializer;
use TinyRest\Sort\SortField;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\TransferObjectInterface;

class RequestHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    /**
     * Same groups as in ValidatorInterface
     */
    private $validationGroups = null;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PaginationFactory $paginationFactory,
        SerializerInterface $serializer
    )
    {
        $this->entityManager     = $entityManager;
        $this->validator         = $validator;
        $this->paginationFactory = $paginationFactory;
        $this->providerFactory   = new ProviderFactory($this->entityManager);
        $this->serializer        = $serializer;
    }

    /**
     * @param Request $request
     * @param object|null $transferObject
     * @param ProviderInterface $dataProvider
     * @param SortField[] $sortFields
     *
     * @return PaginatedCollection
     */
    public function getPaginatedList(
        Request $request,
        ?object $transferObject,
        ProviderInterface $dataProvider,
        array $sortFields = []
    ): PaginatedCollection
    {
        if ($transferObject) {
            $this->handleTransferObject($request, $transferObject);
            $dataProvider->setFilter($transferObject);

            if (empty($sortFields) && $transferObject instanceof SortableListTransferObjectInterface) {
                trigger_error(
                    'Using SortableListTransferObjectInterface is deprecated and will be removed in 2.0, pass $sortFields as a 4th parameter instead',
                    E_USER_DEPRECATED
                );
                $sortFields = $transferObject->getAllowedToSort();
            }
        }

        $pagination = PaginationModel::createFromRequest($request);
        $sort       = SortModel::createFromRequest($request, $sortFields);

        $dataProvider->setSort($sort);

        return $this->paginationFactory->createCollection($pagination, $dataProvider);
    }

    public function getCollection(Request $request, string $apiResource): string
    {
        $apiResourceHandler = new ApiResourceHandler($this->entityManager, $this->paginationFactory, $this->serializer);

        return $apiResourceHandler->handle($request, $apiResource);
    }

    /**
     * @param Request $request
     * @param TransferObjectInterface|null $transferObject
     * @param ProviderInterface $dataProvider
     *
     * @deprecated since 1.4 will be removed in 2.0
     *
     * @return Collection
     */
    public function getList(Request $request, ?TransferObjectInterface $transferObject, ProviderInterface $dataProvider) : Collection
    {
        if (null === $transferObject) {
            $transferObject = new class implements TransferObjectInterface
            {
            };
        } else {
            $this->handleTransferObject($request, $transferObject);
        }

        return new Collection($dataProvider->toArray($transferObject));
    }

    /**
     * @param Request $request
     * @param TransferObjectInterface $transferObject
     * @param $entity
     *
     * @return object
     * @throws ValidationException
     */
    public function create(Request $request, TransferObjectInterface $transferObject, $entity)
    {
        $this->handleTransferObject($request, $transferObject);

        $entityHydrator = new EntityHydrator($this->entityManager);
        $entityHydrator->hydrate($transferObject, $entity);

        $this->validateObject($entity, null, $this->validationGroups);

        return $entity;
    }

    /**
     * @param Request $request
     * @param TransferObjectInterface $transferObject
     * @param $entity
     *
     * @return object
     * @throws ValidationException
     */
    public function update(Request $request, TransferObjectInterface $transferObject, $entity)
    {
        $this->handleTransferObject($request, $transferObject);

        $entityHydrator = new EntityHydrator($this->entityManager);
        $entityHydrator->hydrate($transferObject, $entity, !$request->isMethod('PATCH'));

        $this->validateObject($entity, null, $this->validationGroups);

        return $entity;
    }

    /**
     * @param Request $request
     * @param object $transferObject
     *
     * @return TransferObjectInterface
     * @throws ValidationException
     */
    public function handleTransferObject(Request $request, object $transferObject) : TransferObjectInterface
    {
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->handleRequest($request);

        $this->validateObject($transferObject, null, $this->validationGroups);
        $transferObjectHydrator->runOnObjectValidCallbacks();

        return $transferObject;
    }

    /**
     * @return ProviderFactory
     */
    public function getProviderFactory() : ProviderFactory
    {
        return $this->providerFactory;
    }

    /**
     * @param $groups
     *
     * @return RequestHandler
     */
    public function setValidationGroups($groups) : self
    {
        $this->validationGroups = $groups;

        return $this;
    }

    /**
     * @param $object
     * @param null $constraints
     * @param null $groups
     *
     * @return ConstraintViolationListInterface
     * @throws ValidationException
     */
    public function validateObject($object, $constraints = null, $groups = null) : ConstraintViolationListInterface
    {
        $violations = $this->validator->validate($object, $constraints, $groups);

        if ($violations->count()) {
            throw new ValidationException($violations);
        }

        return $violations;
    }
}

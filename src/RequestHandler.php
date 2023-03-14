<?php

namespace TinyRest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Model\PaginationModel;
use TinyRest\Model\SortModel;
use TinyRest\Provider\ProviderInterface;
use TinyRest\ListCollection\Collection;
use TinyRest\Exception\ValidationException;
use TinyRest\Hydrator\EntityHydrator;
use TinyRest\Hydrator\TransferObjectHydrator;
use TinyRest\Pagination\PaginatedCollection;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\Provider\ProviderFactory;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\TransferObjectInterface;

class RequestHandler
{
    private ProviderFactory $providerFactory;

    private array|GroupSequence|null $validationGroups = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly PaginationFactory      $paginationFactory
    )
    {
        $this->providerFactory = new ProviderFactory($this->entityManager);
    }

    public function getPaginatedList(
        Request                  $request,
        ?TransferObjectInterface $transferObject,
        ProviderInterface        $dataProvider,
        array                    $sortFields = []
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

    public function getList(Request $request, ?TransferObjectInterface $transferObject, ProviderInterface $dataProvider, array $sortFields = []) : Collection
    {
        if (null === $transferObject) {
            $transferObject = new class implements TransferObjectInterface
            {
            };
        } else {
            $this->handleTransferObject($request, $transferObject);
        }

        $dataProvider->setFilter($transferObject);
        $dataProvider->setSort(SortModel::createFromRequest($request, $sortFields));

        return new Collection($dataProvider->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request, TransferObjectInterface $transferObject, $entity) : mixed
    {
        $this->handleTransferObject($request, $transferObject);

        $entityHydrator = new EntityHydrator($this->entityManager);
        $entityHydrator->hydrate($transferObject, $entity);

        $this->validateObject($entity, null, $this->validationGroups);

        return $entity;
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, TransferObjectInterface $transferObject, $entity) : mixed
    {
        $this->handleTransferObject($request, $transferObject);

        $entityHydrator = new EntityHydrator($this->entityManager);
        $entityHydrator->hydrate(
            $transferObject,
            $entity,
            !$request->isMethod('PATCH'),
            array_keys(array_filter(TransferObjectHydrator::payload($request), fn ($value) => null === $value))
        );

        $this->validateObject($entity, null, $this->validationGroups);

        return $entity;
    }

    /**
     * @throws ValidationException
     */
    public function handleTransferObject(Request $request, TransferObjectInterface $transferObject) : object
    {
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->handleRequest($request);

        $this->validateObject($transferObject, null, $this->validationGroups);
        $transferObjectHydrator->runOnObjectValidCallbacks();

        return $transferObject;
    }

    public function getProviderFactory() : ProviderFactory
    {
        return $this->providerFactory;
    }

    public function setValidationGroups(array|GroupSequence $groups) : self
    {
        $this->validationGroups = $groups;

        return $this;
    }

    /**
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

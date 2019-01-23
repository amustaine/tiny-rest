<?php

namespace TinyRest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TinyRest\Provider\ProviderInterface;
use TinyRest\ListCollection\Collection;
use TinyRest\Exception\ValidationException;
use TinyRest\Hydrator\EntityHydrator;
use TinyRest\Hydrator\TransferObjectHydrator;
use TinyRest\Pagination\PaginatedCollection;
use TinyRest\Pagination\PaginationFactory;
use TinyRest\Provider\ProviderFactory;
use TinyRest\TransferObject\PaginatedListTransferObjectInterface;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PaginationFactory $paginationFactory
    )
    {
        $this->entityManager     = $entityManager;
        $this->validator         = $validator;
        $this->paginationFactory = $paginationFactory;
        $this->providerFactory   = new ProviderFactory($this->entityManager);
    }

    /**
     * @param Request $request
     * @param PaginatedListTransferObjectInterface $transferObject
     * @param ProviderInterface $dataProvider
     *
     * @return PaginatedCollection
     * @throws ValidationException
     */
    public function getPaginatedList(
        Request $request,
        PaginatedListTransferObjectInterface $transferObject,
        ProviderInterface $dataProvider
    ) : PaginatedCollection
    {
        $this->handleTransferObject($request, $transferObject);

        return $this->paginationFactory->createCollection($transferObject, $dataProvider);
    }

    /**
     * @param Request $request
     * @param PaginatedListTransferObjectInterface $transferObject
     * @param ProviderInterface $dataProvider
     *
     * @return Collection
     */
    public function getList(Request $request, PaginatedListTransferObjectInterface $transferObject, ProviderInterface $dataProvider) : Collection
    {
        $this->handleTransferObject($request, $transferObject);

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

        $this->validateObject($entity);

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

        $this->validateObject($entity);

        return $entity;
    }

    /**
     * @param Request $request
     * @param TransferObjectInterface $transferObject
     *
     * @return TransferObjectInterface
     * @throws ValidationException
     */
    public function handleTransferObject(Request $request, TransferObjectInterface $transferObject) : TransferObjectInterface
    {
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->validateObject($transferObject);
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
     * @param $object
     *
     * @return ConstraintViolationListInterface
     * @throws ValidationException
     */
    public function validateObject($object) : ConstraintViolationListInterface
    {
        $violations = $this->validator->validate($object);

        if ($violations->count()) {
            throw new ValidationException($violations);
        }

        return $violations;
    }
}

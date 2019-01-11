<?php

namespace TinyRest;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TinyRest\TransferObject\TransferObjectInterface;

class ResponseContainer
{
    /**
     * @var bool
     */
    private $valid;

    /**
     * @var TransferObjectInterface
     */
    private $transferObject;

    /**
     * @var ConstraintViolationListInterface
     */
    private $constraintViolationList;

    public function __construct(TransferObjectInterface $transferObject, ConstraintViolationListInterface $constraintViolationList)
    {
        $this->transferObject          = $transferObject;
        $this->constraintViolationList = $constraintViolationList;

        $this->valid = $constraintViolationList->count() === 0;
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->valid;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolationList() : ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }

    /**
     * @throws HttpException
     */
    public function throwValidationError()
    {
        $violation = $this->constraintViolationList->get(0);

        throw new HttpException(400, $violation->getMessage());
    }
}

<?php

namespace TinyRest\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $this->constraintViolationList = $constraintViolationList;

        parent::__construct('Validation error!');
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolationList() : ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }
}

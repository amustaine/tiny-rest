<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\UpdateTransferObjectInterface;

class ArtistUpdateTransferObject implements UpdateTransferObjectInterface
{
    /**
     * @Property()
     * @Assert\NotBlank()
     */
    public $year;
}

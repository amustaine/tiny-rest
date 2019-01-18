<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

class ArtistUpdateTransferObject implements TransferObjectInterface
{
    /**
     * @Property()
     * @Assert\NotBlank()
     */
    public $year;
}

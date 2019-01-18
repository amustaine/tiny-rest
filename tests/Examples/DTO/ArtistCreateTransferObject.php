<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

class ArtistCreateTransferObject implements TransferObjectInterface
{
    /**
     * @Property()
     * @Assert\NotBlank()
     */
    public $genre;

    /**
     * @Property()
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Property()
     * @Assert\NotBlank()
     */
    public $year;
}

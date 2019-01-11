<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\Tests\Examples\Entity\Artist;
use TinyRest\TransferObject\CreateTransferObjectInterface;

class ArtistCreateTransferObject implements CreateTransferObjectInterface
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

    public function getCreateEntityClassName() : string
    {
        return Artist::class;
    }
}

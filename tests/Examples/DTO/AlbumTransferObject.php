<?php

namespace TinyRest\Tests\Examples\DTO;

use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\TransferObject\CreateTransferObjectInterface;
use TinyRest\TransferObject\UpdateTransferObjectInterface;

class AlbumTransferObject implements CreateTransferObjectInterface, UpdateTransferObjectInterface
{
    /**
     * @Property()
     */
    public $name;

    /**
     * @Property()
     */
    public $year;

    /**
     * @Property()
     * @Relation(class="TinyRest\Tests\Examples\Entity\Artist", byField="name")
     */
    public $artist;

    /**
     * @Property()
     * @Relation(class="TinyRest\Tests\Examples\Entity\Cover")
     */
    public $cover;
}

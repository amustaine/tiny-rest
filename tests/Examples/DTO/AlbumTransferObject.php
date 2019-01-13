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
     * @Relation(byField="name")
     */
    public $artist;

    /**
     * @Property()
     */
    public $cover;

    /**
     * @Property()
     */
    public $releaseDate;
}

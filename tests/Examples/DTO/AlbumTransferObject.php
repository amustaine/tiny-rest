<?php

namespace TinyRest\Tests\Examples\DTO;

use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\TransferObject\TransferObjectInterface;

class AlbumTransferObject implements TransferObjectInterface
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

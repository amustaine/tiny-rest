<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\PaginatedListTransferObjectInterface;
use TinyRest\TransferObject\PaginationTrait;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\SortTrait;

class SongPaginatedListTransferObject implements PaginatedListTransferObjectInterface, SortableListTransferObjectInterface
{
    use PaginationTrait, SortTrait;

    /**
     * @Property()
     */
    public $name;

    /**
     * @Assert\Range(min="1000", max="9999")
     * @Property()
     */
    public $year;

    public function getAllowedToSort() : array
    {
        return ['id'];
    }
}

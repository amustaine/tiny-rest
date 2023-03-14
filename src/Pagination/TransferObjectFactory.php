<?php

namespace TinyRest\Pagination;

use TinyRest\Sort\SortField;
use TinyRest\TransferObject\PaginatedListTransferObjectInterface;
use TinyRest\TransferObject\PaginationTrait;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\SortTrait;
use TinyRest\TransferObject\TransferObjectInterface;

class TransferObjectFactory
{
    private array $allowedToSort = [];

    public function addSortField(string $field, ...$aliases)
    {
        $this->allowedToSort[] = new SortField($field, $aliases);

        return $this;
    }

    public function create() : TransferObjectInterface
    {
        return new class($this->allowedToSort) implements SortableListTransferObjectInterface, PaginatedListTransferObjectInterface
        {
            use SortTrait, PaginationTrait;

            private $allowedToSort;

            public function __construct(array $allowedToSort)
            {
                $this->allowedToSort = $allowedToSort;
            }

            public function getAllowedToSort() : array
            {
                return $this->allowedToSort;
            }
        };
    }
}

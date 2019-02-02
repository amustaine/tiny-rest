<?php

namespace TinyRest\Tests\Examples\DTO;

use TinyRest\Sort\SortField;
use TinyRest\TransferObject\SortableListTransferObjectInterface;
use TinyRest\TransferObject\SortTrait;
use TinyRest\TransferObject\TransferObjectInterface;

class ProductTransferObject implements TransferObjectInterface, SortableListTransferObjectInterface
{
    use SortTrait;

    /**
     * {@inheritdoc}
     */
    public function getSort() : ?string
    {
        return 'p.'. $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedToSort() : array
    {
        return [
            'p.weight',
            new SortField('p.price', 'price', 'prc')
        ];
    }
}

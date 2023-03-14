<?php

namespace TinyRest\TransferObject;

use TinyRest\Annotations\Property;

trait SortTrait
{
    #[Property]
    private ?string $sort = null;

    #[Property]
    private ?string $sortDir = null;

    public function getSort() : ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort) : void
    {
        $this->sort = $sort;
    }

    public function getSortDir() : ?string
    {
        return $this->sortDir;
    }

    public function setSortDir(?string $sortDir) : void
    {
        $this->sortDir = $sortDir;
    }
}

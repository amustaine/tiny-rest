<?php

namespace TinyRest\Provider;

use TinyRest\Model\SortModel;

trait SortTrait
{
    private ?SortModel $sort = null;

    public function validSort() : bool
    {
        return null !== $this->sort?->getField();
    }

    public function setSort(SortModel $sortModel) : void
    {
        $this->sort = $sortModel;
    }
}

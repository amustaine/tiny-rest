<?php

namespace TinyRest\Provider;

use TinyRest\Model\SortModel;

trait SortTrait
{
    /**
     * @var SortModel
     */
    private $sort;

    public function validSort(): bool
    {
        return $this->sort && $this->sort->getField();
    }

    public function setSort(SortModel $sortModel)
    {
        $this->sort = $sortModel;
    }
}

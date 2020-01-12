<?php

namespace TinyRest\Provider;

use TinyRest\Model\SortModel;

trait SortTrait
{
    /**
     * @var SortModel
     */
    private $sort;

    public function setSort(SortModel $sortModel)
    {
        $this->sort = $sortModel;
    }
}

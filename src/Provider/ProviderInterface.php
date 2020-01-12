<?php

namespace TinyRest\Provider;

use TinyRest\Model\SortModel;

interface ProviderInterface
{
    public function provide();

    public function setFilter(object $filter);

    public function setSort(SortModel $sortModel);

    public function toArray(): array;
}

<?php

namespace TinyRest\Provider;

trait FilterTrait
{
    private $filter;

    public function setFilter(object $filter)
    {
        $this->filter = $filter;
    }
}

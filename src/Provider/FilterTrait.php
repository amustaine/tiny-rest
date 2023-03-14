<?php

namespace TinyRest\Provider;

trait FilterTrait
{
    private mixed $filter;

    public function setFilter(object $filter) : void
    {
        $this->filter = $filter;
    }
}

<?php

namespace TinyRest\Provider;

abstract class ArrayProvider implements ProviderInterface
{
    use SortTrait, FilterTrait;

    abstract public function provide(): array;

    public function toArray(): array
    {
        return $this->provide();
    }
}

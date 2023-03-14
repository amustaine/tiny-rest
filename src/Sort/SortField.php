<?php

namespace TinyRest\Sort;

class SortField
{
    private array $aliases;

    public function __construct(private readonly string $field, ...$aliases)
    {
        $this->aliases = $aliases;
    }

    public function getFieldByAlias(string $alias) : ?string
    {
        if (in_array($alias, $this->aliases)) {
            return $this->field;
        }

        return null;
    }
}

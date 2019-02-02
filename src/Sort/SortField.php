<?php

namespace TinyRest\Sort;

class SortField
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $aliases;

    public function __construct(string $field, ...$aliases)
    {
        $this->field   = $field;
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

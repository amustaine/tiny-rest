<?php

namespace TinyRest\Annotations;

/**
 * @Annotation
 */
class Property
{
    const TYPE_STRING     = 'string';
    const TYPE_ARRAY      = 'array';
    const TYPE_INTEGER    = 'integer';
    const TYPE_FLOAT      = 'float';
    const TYPE_BOOLEAN    = 'boolean';
    const TYPE_DATETIME   = 'datetime';
    const COMMA_SEPARATED = 'commaSeparated';

    public $name;

    public $type;

    public $mapped = true;

    public $extra = [];
}

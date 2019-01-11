<?php

namespace TinyRest\Annotations;

/**
 * @Annotation
 */
class Property
{
    const TYPE_ARRAY = 'array';

    public $name;

    public $type;

    public $mappedBy;

    public $columnName;

    public $filterable;
}

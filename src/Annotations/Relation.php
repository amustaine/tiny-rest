<?php

namespace TinyRest\Annotations;

/**
 * @Annotation
 */
class Relation
{
    public $class;

    public $byField = 'id';
}

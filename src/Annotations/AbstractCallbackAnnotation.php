<?php

namespace TinyRest\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
abstract class AbstractCallbackAnnotation
{
    public $method;

    public $callback;
}

<?php

namespace TinyRest\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class OnObjectHydrated
{
    public $method;

    public $callback;
}

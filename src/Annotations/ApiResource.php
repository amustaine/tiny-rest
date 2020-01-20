<?php

namespace TinyRest\Annotations;

use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("normalizationContext", type="array"),
 *     @Attribute("denormalizationContext", type="array"),
 * })
 */
class ApiResource
{
    /**
     * @var array
     */
    public $normalizationContext;

    /**
     * @var array
     */
    public $denormalizationContext;
}

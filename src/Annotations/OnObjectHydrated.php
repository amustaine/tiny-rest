<?php

namespace TinyRest\Annotations;

use Attribute;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[Attribute(Attribute::TARGET_CLASS)]
class OnObjectHydrated extends AbstractCallbackAnnotation
{
}

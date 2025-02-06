<?php

namespace TinyRest\Annotations;

use Attribute;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OnObjectHydrated extends AbstractCallbackAnnotation
{
}

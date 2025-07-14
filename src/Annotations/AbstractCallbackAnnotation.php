<?php

namespace TinyRest\Annotations;

use Attribute;
use TinyRest\Attributes\AttributeInterface;
use TinyRest\Attributes\AttributeTrait;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
abstract class AbstractCallbackAnnotation implements AttributeInterface
{
    use AttributeTrait;

    public function __construct(array $options = [], public ?string $method = null, public ?string $callback = null)
    {
        $this->populateProperties($options);
    }
}

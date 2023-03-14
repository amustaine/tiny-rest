<?php

namespace TinyRest\Annotations;

use Attribute;
use TinyRest\Attributes\AttributeInterface;
use TinyRest\Attributes\AttributeTrait;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Mapping implements AttributeInterface
{
    use AttributeTrait;

    public function __construct(array $options = [], public ?string $column = null, public bool $mapped = true)
    {
        $this->populateProperties($options);
    }
}

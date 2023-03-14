<?php

namespace TinyRest\Annotations;

use Attribute;
use TinyRest\Attributes\AttributeInterface;
use TinyRest\Attributes\AttributeTrait;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Relation implements AttributeInterface
{
    use AttributeTrait;

    public function __construct(array $options = [], public string $byField = 'id')
    {
        $this->populateProperties($options);
    }
}

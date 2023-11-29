<?php

namespace TinyRest\Annotations;

use Attribute;
use InvalidArgumentException;
use TinyRest\Attributes\AttributeInterface;
use TinyRest\Attributes\AttributeTrait;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Property implements AttributeInterface
{
    use AttributeTrait;

    const TYPE_STRING     = 'string';
    const TYPE_ARRAY      = 'array';
    const TYPE_INTEGER    = 'integer';
    const TYPE_FLOAT      = 'float';
    const TYPE_BOOLEAN    = 'boolean';
    const TYPE_DATETIME   = 'datetime';
    const COMMA_SEPARATED = 'commaSeparated';

    public function __construct(
        array $options = [],
        public ?string $name       = null,
        public ?string $type       = null,
        public bool    $mapped     = true,
        public bool    $resettable = false,
        public array   $extra      = [],
    )
    {
        $this->populateProperties($options);

        if (!empty($this->type) && !in_array($this->type, self::allTypes()) && !class_exists($this->type)) {
            throw new InvalidArgumentException("Property type '$this->type' is not supported.");
        }
    }

    public static function allTypes() : array
    {
        return [
            self::TYPE_STRING,
            self::TYPE_ARRAY,
            self::TYPE_INTEGER,
            self::TYPE_FLOAT,
            self::TYPE_BOOLEAN,
            self::TYPE_DATETIME,
            self::COMMA_SEPARATED,
        ];
    }
}

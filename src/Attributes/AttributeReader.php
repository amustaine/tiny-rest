<?php

namespace TinyRest\Attributes;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class AttributeReader
 *
 * @package TinyRest\Attribute
 *
 * @author  Alex Buturlakin <alexbuturlakin@gmail.com>
 */
final class AttributeReader
{
    public function getClassAttributes(ReflectionClass $class): array
    {
        return $this->convertToAttributeInstances($class->getAttributes());
    }

    public function getPropertyAttributes(ReflectionProperty $property): array
    {
        return $this->convertToAttributeInstances($property->getAttributes());
    }

    private function convertToAttributeInstances(array $attributes): array
    {
        $instances = [];

        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();

            assert(is_string($attributeName));

            if (!is_subclass_of($attributeName, AttributeInterface::class)) {
                continue;
            }

            $instance = $attribute->newInstance();

            assert($instance instanceof AttributeInterface);

            $instances[] = $instance;
        }

        return $instances;
    }
}

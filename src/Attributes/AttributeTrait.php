<?php

namespace TinyRest\Attributes;

/**
 * Trait AttributeTrait
 *
 * @package TinyRest\Attributes
 *
 * @author  Alex Buturlakin <alexbuturlakin@gmail.com>
 */
trait AttributeTrait
{
    public function populateProperties(?array $options = null) : void
    {
        if (empty($options)) {
            return;
        }

        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }
}

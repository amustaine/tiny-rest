<?php

namespace TinyRest\Request;

use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractCreateRequest extends AbstractRequest
{
    public function createEntity()
    {
        if (!$this->readAnnotations) {
            $this->readAnnotations();
        }

        $pa = new PropertyAccessor();

        $target = $this->getTargetEntity();
        $entity = new $target;

        foreach ($this->getProperties() as $property) {
            $value = $pa->getValue($this, $property['name']);

            if (null !== $value) {
                $pa->setValue($entity, $property['mappedBy'] ?: $property['name'], $pa->getValue($this, $property['name']));
            }
        }

        return $entity;
    }
}

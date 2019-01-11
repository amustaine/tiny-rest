<?php

namespace TinyRest\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractUpdateRequest extends AbstractRequest
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

    public function updateEntity($entity)
    {
        $this->clearFields($entity);
    }

    protected function hydrate($entity, array $properties)
    {
        $pa = new PropertyAccessor();

        foreach ($properties as $property) {
            $value = $pa->getValue($this, $property['name']);

            if (null !== $value) {
                $pa->setValue($entity, $property['mappedBy'] ?: $property['name'], $pa->getValue($this, $property['name']));
            }
        }
    }

    private function clearFields($entity)
    {
        $annotationReader = new AnnotationReader();
        $reflection       = new \ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            if ($annotationReader->getPropertyAnnotation($property, Id::class)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($entity, null);
            $property->setAccessible(false);
        }
    }
}

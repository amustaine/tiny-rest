<?php

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Attributes\AttributeReader;
use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\OnObjectHydrated;
use TinyRest\Annotations\OnObjectValid;
use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use ReflectionClass;

class MetaReader
{
    private AnnotationReader $annotationReader;
    private AttributeReader  $attributeReader;
    private ReflectionClass  $reflection;

    private array $properties       = [];
    private array $relations        = [];
    private array $mapping          = [];
    private array $onObjectHydrated = [];
    private array $onObjectValid    = [];

    public function __construct(object $transferObject)
    {
        $this->annotationReader = new AnnotationReader();
        $this->attributeReader  = new AttributeReader();
        $this->reflection       = new ReflectionClass($transferObject);

        $this->handleClassAnnotations();
        $this->handlePropertyAnnotations();
    }

    private function handleClassAnnotations() : void
    {
        $annotations = $this->annotationReader->getClassAnnotations($this->reflection);
        $attributes  = $this->attributeReader->getClassAttributes($this->reflection);

        foreach (array_merge($annotations, $attributes) as $instance) {
            if ($instance instanceof OnObjectHydrated) {
                $this->onObjectHydrated[] = $instance;
            }

            if ($instance instanceof OnObjectValid) {
                $this->onObjectValid[] = $instance;
            }
        }
    }

    private function handlePropertyAnnotations() : void
    {
        foreach ($this->reflection->getProperties() as $property) {
            $annotations  = $this->annotationReader->getPropertyAnnotations($property);
            $attributes   = $this->attributeReader->getPropertyAttributes($property);
            $propertyName = $property->getName();

            foreach (array_merge($annotations, $attributes) as $instance) {
                if ($instance instanceof Property) {
                    if (empty($instance->name)) {
                        $instance->name = $propertyName;
                    }

                    $this->properties[$propertyName] = $instance;
                }

                if ($instance instanceof Relation) {
                    $this->relations[$propertyName] = $instance;
                }

                if ($instance instanceof Mapping) {
                    $this->mapping[$propertyName] = $instance;
                }
            }

            if (isset($this->properties[$propertyName])) {
                if (empty($this->mapping[$propertyName])) {
                    $mapping         = new Mapping();
                    $mapping->column = $propertyName;

                    $this->mapping[$propertyName] = $mapping;
                }
            } else {
                if (isset($this->mapping[$propertyName])) {
                    unset($this->mapping[$propertyName]);
                }

                if (isset($this->relations[$propertyName])) {
                    unset($this->relations[$propertyName]);
                }
            }
        }
    }

    /**
     * @return Property[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return Relation[]
     */
    public function getRelations() : array
    {
        return $this->relations;
    }

    /**
     * @return Mapping[]
     */
    public function getMapping() : array
    {
        return $this->mapping;
    }

    /**
     * @return OnObjectHydrated[]
     */
    public function getOnObjectHydratedAnnotations() : array
    {
        return $this->onObjectHydrated;
    }

    /**
     * @return OnObjectValid[]
     */
    public function getOnObjectValidAnnotations() : array
    {
        return $this->onObjectValid;
    }
}

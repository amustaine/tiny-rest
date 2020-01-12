<?php

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\OnObjectHydrated;
use TinyRest\Annotations\OnObjectValid;
use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\TransferObject\TransferObjectInterface;

class MetaReader
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    private $properties = [];

    private $relations = [];

    private $mapping = [];

    private $onObjectHydrated = [];

    private $onObjectValid = [];

    public function __construct(object $transferObject)
    {
        $this->annotationReader = new AnnotationReader();
        $this->reflection       = new \ReflectionClass($transferObject);

        $this->handleClassAnnotations();
        $this->handlePropertyAnnotations();
    }

    private function handleClassAnnotations()
    {
        $classAnnotations = $this->annotationReader->getClassAnnotations($this->reflection);

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof OnObjectHydrated) {
                $this->onObjectHydrated[] = $annotation;
            } elseif ($annotation instanceof OnObjectValid) {
                $this->onObjectValid[] = $annotation;
            }
        }
    }

    private function handlePropertyAnnotations()
    {
        foreach ($this->reflection->getProperties() as $property) {
            $annotations  = $this->annotationReader->getPropertyAnnotations($property);
            $propertyName = $property->getName();

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Property) {
                    if (empty($annotation->name)) {
                        $annotation->name = $propertyName;
                    }

                    $this->properties[$propertyName] = $annotation;
                } elseif ($annotation instanceof Relation) {
                    $this->relations[$propertyName] = $annotation;
                } elseif ($annotation instanceof Mapping) {
                    $this->mapping[$propertyName] = $annotation;
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

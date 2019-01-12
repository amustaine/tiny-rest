<?php

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\OnObjectHydrated;
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

    public function __construct(TransferObjectInterface $transferObject)
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

            if (empty($this->mapping[$propertyName])) {
                $mapping         = new Mapping();
                $mapping->column = $propertyName;

                $this->mapping[$propertyName] = $mapping;
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
}

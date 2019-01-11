<?php

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\TransferObject\TransferObjectInterface;

class PropertyReader
{
    private $properties = [];

    private $relations = [];

    private $filterable = [];

    private function readAnnotations($class)
    {
        $annotationReader = new AnnotationReader();

        $reflection = new \ReflectionClass($class);

        foreach ($reflection->getProperties() as $property) {
            $annotations  = $annotationReader->getPropertyAnnotations($property);
            $propertyName = $property->getName();

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Property) {
                    $this->properties[$propertyName] = [
                        'name'      => $propertyName,
                        'paramName' => $annotation->name ?: $propertyName,
                        'type'      => $annotation->type,
                        'mappedBy'  => $annotation->mappedBy
                    ];

                    if ($annotation->filterable) {
                        $this->filterable[] = $propertyName;
                    }

                } elseif ($annotation instanceof Relation) {
                    $this->relations[$propertyName] = [
                        'class'   => $annotation->class,
                        'byField' => $annotation->byField
                    ];
                }
            }
        }
    }

    public function __construct(TransferObjectInterface $transferObject)
    {
        $this->readAnnotations($transferObject);
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getRelations() : array
    {
        return $this->relations;
    }

    /**
     * @return array
     */
    public function getFilterable() : array
    {
        return $this->filterable;
    }
}

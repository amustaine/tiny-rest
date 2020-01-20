<?php

namespace TinyRest\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;

class ResourceReader
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var array
     */
    private $sortFields = [];

    /**
     * @var array
     */
    private $normalizationContext = [];

    public function __construct(string $className)
    {
        $this->annotationReader = new AnnotationReader();

        $this->read($className);
    }

    public function read(string $class)
    {
        $ref = new \ReflectionClass($class);
        $obj = $this->annotationReader->getClassAnnotation($ref, ApiResource::class);

        if (!$obj instanceof ApiResource) {
            throw new \InvalidArgumentException('The class should be annotated with @ApiResource');
        }

        $this->normalizationContext = $obj->normalizationContext;

        $this->readProperties($ref);
    }

    private function readProperties(\ReflectionClass $ref)
    {
        foreach ($ref->getProperties() as $property) {
            $column = $this->annotationReader->getPropertyAnnotation($property, Column::class);

            if ($column) {
                $this->sortFields[] = $property->getName();
            }
        }
    }

    public function getSortFields(): array
    {
        return $this->sortFields;
    }

    public function getNormalizationContext(): array
    {
        return $this->normalizationContext;
    }
}

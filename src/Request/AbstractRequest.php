<?php

namespace TinyRest\Request;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Annotations\Entity;
use TinyRest\Annotations\Property;

abstract class AbstractRequest
{
    protected $properties;

    private $target;

    private $body;

    protected $readAnnotations = false;

    public function handleRequest(Request $request)
    {
        $this->readAnnotations();

        if (!$request->isMethod('GET')) {
            $this->readFromBody($request);
        }

        $propertyAccessor = new PropertyAccessor();
        foreach ($this->properties as $property) {
            $paramName = $property['paramName'];
            $value = $this->body[$paramName] ?? $request->query->get($paramName);

            if (Property::TYPE_ARRAY === $property['type']) {
                $value = $this->parseProperties($value);
            }

            $propertyAccessor->setValue($this, $property['name'], $value);
        }
    }

    protected function readAnnotations()
    {
        $annotationReader = new AnnotationReader();

        $reflection = new \ReflectionClass($this);

        $classAnnotation = $annotationReader->getClassAnnotation($reflection, Entity::class);
        if ($classAnnotation) {
            $this->target = $classAnnotation->target;
        }

        foreach ($reflection->getProperties() as $property) {
            $annotations = $annotationReader->getPropertyAnnotations($property);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Property) {
                    $this->properties[] = [
                        'name'      => $property->getName(),
                        'paramName' => $annotation->name ?: $property->getName(),
                        'type'      => $annotation->type,
                        'mappedBy'  => $annotation->mappedBy
                    ];
                }
            }
        }

        $this->readAnnotations = true;
    }



    private static function parseProperties(?string $props) : ?array
    {
        $data = [];

        if (!$props) {
            return null;
        }

        $properties = explode(',', $props);
        foreach ($properties as $property) {
            $data[] = trim($property);
        }

        return $data;
    }

    protected function getProperties() : array
    {
        return $this->properties;
    }
}

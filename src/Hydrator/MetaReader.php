<?php

/*
 * This file is part of the DataTables Backend package.
 *
 * (c) TinyRest <https://github.com/RuSS-B/tiny-rest>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\OnObjectHydrated;
use TinyRest\Annotations\OnObjectValid;
use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\TransferObject\TransferObjectInterface;

/**
 *  @author Russ Balabanov <russ.developer@gmail.com>
 */
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

    /**
     * @var ObjectMeta
     */
    private $objectMeta;

    public function __construct(TransferObjectInterface $transferObject)
    {
        $this->annotationReader = new AnnotationReader();
        $this->reflection       = new \ReflectionClass($transferObject);

        $this->handleClassAnnotations();
        $this->handlePropertyAnnotations();

        $this->objectMeta = new ObjectMeta($this->properties, $this->relations, $this->mapping, $transferObject);
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

    public function getObjectMeta() : ObjectMeta
    {
        return $this->objectMeta;
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

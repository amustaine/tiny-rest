<?php

/*
 * This file is part of the DataTables Backend package.
 *
 * (c) TinyRest <https://github.com/RuSS-B/tiny-rest>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyRest\Converter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use TinyRest\Annotations\Relation;
use TinyRest\Hydrator\ObjectMeta;

/**
 *  @author Russ Balabanov <russ.developer@gmail.com>
 */
class EntityConverter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createObjectMetaFromEntity(string $className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $notMapped = $this->getNotMapped($classMetadata);

        $properties = array_merge($classMetadata->getFieldNames(), $notMapped);
        $relations  = $this->getRelations($classMetadata->getAssociationMappings());
        $mapping    = array_merge($properties, array_keys($relations));

        //@todo should be removed in v2.0 after annotation class won't be a part of mapping
        $mapping = $this->adaptForAnnotations($mapping);

        return new ObjectMeta($properties, $relations, $mapping, $data);
    }

    private function getNotMapped(ClassMetadata $classMetadata) : array
    {
        $allProperties = $classMetadata->getReflectionClass()->getProperties();

        $notMapped = array_map(function ($item) {
            return $item->name;
        }, $allProperties);

        $notMapped = array_diff($notMapped, $classMetadata->getFieldNames(), $classMetadata->getAssociationNames());

        return array_values($notMapped);
    }

    private function getRelations(array $associationMappings) : array
    {
        $relations = [];

        foreach ($associationMappings as $associationMapping) {
            $relation = new Relation();
            $relation->byField = $associationMapping['joinColumns'][0]['referencedColumnName'];
            $relations[$associationMapping['fieldName']] = $relation;
        }

        return $relations;
    }

    private function adaptForAnnotations(array $mapping) : array
    {
        $data = [];

        foreach ($mapping as $column) {
            $annotation = new \stdClass();
            $annotation->mapped = true;
            $annotation->column = $column;

            $data[$column] = $annotation;
        }

        return $data;
    }
}

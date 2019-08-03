<?php

namespace TinyRest\Converter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use TinyRest\Annotations\Relation;
use TinyRest\Hydrator\ObjectMeta;

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
}

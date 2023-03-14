<?php

namespace TinyRest\Hydrator;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ClassMetadata;
use TinyRest\TransferObject\TransferObjectInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntityHydrator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function hydrate(TransferObjectInterface $transferObject, mixed $entity, ?bool $clearFields = false, array $reset = []) : void
    {
        if (!is_object($entity)) {
            throw new \InvalidArgumentException('$entity should be a doctrine Entity object class');
        }

        $metaReader = new MetaReader($transferObject);
        $relations  = $metaReader->getRelations();
        $properties = $metaReader->getProperties();

        $propertyAccessor = new PropertyAccessor();
        $entityMetadata   = $this->entityManager->getClassMetadata(get_class($entity));

        if (true === $clearFields) {
            $this->clearFields($entity, $entityMetadata);
        }

        foreach ($metaReader->getMapping() as $propertyName => $annotation) {
            if (!$annotation->mapped) {
                continue;
            }

            $value = $propertyAccessor->getValue($transferObject, $propertyName);

            if (null === $value && (!$properties[$propertyName]?->resettable || !in_array($propertyName, $reset))) {
                continue;
            }

            if (!$entityMetadata->hasAssociation($annotation->column)) {
                if ($entityMetadata->hasField($annotation->column)) {
                    $fieldMapping = $entityMetadata->getFieldMapping($annotation->column);
                    $value        = $this->castValue($value, $fieldMapping);
                }
            } else {
                $relation = $relations[$propertyName] ?? null;
                $byField  = $relation ? $relation->byField : $entityMetadata->getIdentifier()[0];
                $value    = $this->loadRelation($entityMetadata->getAssociationTargetClass($annotation->column), $byField, $value);
            }

            $propertyAccessor->setValue($entity, $annotation->column, $value);
        }
    }

    private function castValue($value, array $fieldMapping) : mixed
    {
        if (is_string($value) && in_array($fieldMapping['type'], [
                Types::DATE_IMMUTABLE,
                Types::DATE_MUTABLE,
                Types::DATETIME_IMMUTABLE,
                Types::DATETIME_MUTABLE,
                Types::DATETIMETZ_IMMUTABLE,
                Types::DATETIMETZ_MUTABLE
            ])) {
            $value = (new TypeCaster())->getDateTime($value);
        }

        return $value;
    }

    private function loadRelation(string $class, string $byField, $value) : mixed
    {
        return $this->entityManager->getRepository($class)->findOneBy([$byField => $value]);
    }

    private function clearFields($entity, ClassMetadata $classMetadata) : void
    {
        $reflection = $classMetadata->getReflectionClass();

        foreach ($reflection->getProperties() as $property) {
            if (in_array($property->getName(), $classMetadata->getIdentifier())) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($entity, null);
            $property->setAccessible(false);
        }
    }
}

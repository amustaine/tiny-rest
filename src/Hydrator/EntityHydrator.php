<?php

namespace TinyRest\Hydrator;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use TinyRest\TransferObject\TransferObjectInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntityHydrator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TransferObjectInterface $transferObject
     * @param object $entity
     * @param bool|null $clearFields
     */
    public function hydrate(TransferObjectInterface $transferObject, $entity, ?bool $clearFields = false)
    {
        if (!is_object($entity)) {
            throw new \InvalidArgumentException('$entity should be a doctrine Entity object class');
        }

        $metaReader = new MetaReader($transferObject);
        $relations  = $metaReader->getRelations();

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

            if (null !== $value) {
                if (!$entityMetadata->hasAssociation($annotation->column)) {
                    $fieldMapping = $entityMetadata->getFieldMapping($annotation->column);
                    if (is_string($value) && in_array($fieldMapping['type'], [
                        Type::DATE,
                        Type::DATE_IMMUTABLE,
                        Type::DATETIME,
                        Type::DATETIME_IMMUTABLE,
                        Type::DATETIMETZ,
                        Type::DATETIMETZ_IMMUTABLE
                    ])) {
                        $value = new \DateTime($value);
                    }
                } else {
                    $relation = $relations[$propertyName] ?? null;
                    $byField  = $relation ? $relation->byField : $entityMetadata->getIdentifier()[0];
                    $value    = $this->loadRelation($entityMetadata->getAssociationTargetClass($annotation->column), $byField, $value);
                }

                $propertyAccessor->setValue($entity, $annotation->column, $value);
            }
        }
    }

    private function loadRelation(string $class, string $byField, $value)
    {
        return $this->entityManager->getRepository($class)->findOneBy([$byField => $value]);
    }

    /**
     * @param object $entity
     * @param ClassMetadata $classMetadata
     */
    private function clearFields($entity, ClassMetadata $classMetadata)
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

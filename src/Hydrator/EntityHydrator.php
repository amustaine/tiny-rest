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
     * @param ObjectMetaInterface $objectMeta
     * @param object $entity
     * @param bool|null $clearFields
     */
    public function hydrate($objectMeta, $entity, ?bool $clearFields = false)
    {
        if (!$objectMeta instanceof ObjectMetaInterface) {

            if (!$objectMeta instanceof TransferObjectInterface) {
                throw new \InvalidArgumentException(sprintf('Unsupported object given: %s', get_class($objectMeta)));
            }

            $objectMeta = (new MetaReader($objectMeta))->getObjectMeta();

            trigger_error('Passing TransferObjectInterface in hydrate() is deprecated and will be disabled in version 2.0, use ObjectMetaInterface instead', E_USER_DEPRECATED);
        }

        if (!is_object($entity)) {
            throw new \InvalidArgumentException('$entity should be a doctrine Entity object class');
        }

        $relations = $objectMeta->getRelations();

        $propertyAccessor = new PropertyAccessor();
        $entityMetadata   = $this->entityManager->getClassMetadata(get_class($entity));

        if (true === $clearFields) {
            $this->clearFields($entity, $entityMetadata);
        }

        foreach ($objectMeta->getMapping() as $propertyName => $annotation) {
            if (!$annotation->mapped) {
                continue;
            }

            $value = $propertyAccessor->getValue($objectMeta->getData(), $propertyName);

            if (null === $value) {
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

    /**
     * @param $value
     * @param array $fieldMapping
     *
     * @return \DateTime|null|mixed
     */
    private function castValue($value, array $fieldMapping)
    {
        if (is_string($value) && in_array($fieldMapping['type'], [
                Type::DATE,
                Type::DATE_IMMUTABLE,
                Type::DATETIME,
                Type::DATETIME_IMMUTABLE,
                Type::DATETIMETZ,
                Type::DATETIMETZ_IMMUTABLE
            ])) {
            $value = (new TypeCaster())->getDateTime($value);
        }

        return $value;
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

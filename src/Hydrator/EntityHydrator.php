<?php

namespace TinyRest\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use TinyRest\TransferObject\TransferObjectInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
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

    public function hydrate(TransferObjectInterface $transferObject, $entity, ?bool $clearFields = false)
    {
        $propertyReader   = new PropertyReader($transferObject);
        $relations        = $propertyReader->getRelations();
        $propertyAccessor = new PropertyAccessor();

        if (true === $clearFields) {
            $this->clearFields($entity);
        }

        foreach ($propertyReader->getProperties() as $propertyName => $property) {
            $value = $propertyAccessor->getValue($transferObject, $propertyName);

            if (null !== $value) {
                if (isset($relations[$propertyName])) {
                    $value = $this->loadRelation($relations[$propertyName]['class'], $relations[$propertyName]['byField'], $value);
                }

                $propertyAccessor->setValue(
                    $entity,
                    $property['mappedBy'] ?: $property['name'],
                    $value
                );
            }
        }
    }

    private function loadRelation(string $class, string $byField, $value)
    {
        return $this->entityManager->getRepository($class)->findOneBy([$byField => $value]);
    }

    /**
     * @param object $entity
     */
    private function clearFields($entity)
    {
        $annotationReader = new AnnotationReader();
        $reflection       = new \ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            if ($annotationReader->getPropertyAnnotation($property, Id::class)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($entity, null);
            $property->setAccessible(false);
        }
    }
}

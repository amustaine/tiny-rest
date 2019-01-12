<?php

namespace TinyRest\Hydrator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use TinyRest\Annotations\OnObjectHydrated;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

class TransferObjectHydrator
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param TransferObjectInterface $transferObject
     * @param Request $request
     *
     * @throws \Exception
     */
    public function hydrate(TransferObjectInterface $transferObject, Request $request)
    {
        $this->data = $request->isMethod('GET') ? $request->query->all() : $this->getBody($request);

        $metaReader   = new MetaReader($transferObject);
        $propertyAccessor = new PropertyAccessor();

        foreach ($metaReader->getProperties() as $propertyName => $annotation) {
            $value     = $this->getValue($annotation->name);

            if (Property::TYPE_ARRAY === $annotation->type) {
                $value = $this->splitStringToArray($value);
            }

            if ($annotation->mapped) {
                $propertyAccessor->setValue($transferObject, $propertyName, $value);
            }
        }

        $this->runCallbacks($metaReader->getOnObjectHydratedAnnotations(), $transferObject);
    }

    /**
     * @param TransferObjectInterface $transferObject
     * @param OnObjectHydrated[] $onObjectHydrated
     *
     * @throws \Exception
     */
    private function runCallbacks(array $onObjectHydrated, TransferObjectInterface $transferObject)
    {
        foreach ($onObjectHydrated as $event) {
            if (!empty($event->method)) {
                [$transferObject, $event->method]();
            } elseif (is_callable($event->callback)) {
                ($event->callback)($transferObject);
            } else {
                throw new \Exception('Invalid callback');
            }
        }
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    private function getValue(string $property)
    {
        return $this->data[$property] ?? null;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    private function getBody(Request $request) : array
    {
        if (!$request->getContent()) {
            return [];
        }

        $body = json_decode($request->getContent(), true);

        if (json_last_error()) {
            throw new \Exception('Invalid JSON');
        }

        return $body;
    }

    private function splitStringToArray(?string $props) : ?array
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
}

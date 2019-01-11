<?php

namespace TinyRest\Hydrator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

class TransferObjectHydrator
{
    /**
     * @var array
     */
    private $data = [];

    public function hydrate(TransferObjectInterface $transferObject, Request $request)
    {
        $this->data = $request->isMethod('GET') ? $request->query->all() : $this->getBody($request);

        $propertyReader   = new PropertyReader($transferObject);
        $propertyAccessor = new PropertyAccessor();

        foreach ($propertyReader->getProperties() as $property) {
            $paramName = $property['paramName'];
            $value     = $this->getValue($paramName);

            if (Property::TYPE_ARRAY === $property['type']) {
                $value = $this->splitStringToArray($value);
            }

            $propertyAccessor->setValue($transferObject, $property['name'], $value);
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

        if (!$body) {
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

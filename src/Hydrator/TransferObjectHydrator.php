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

    /**
     * @var TransferObjectInterface
     */
    private $transferObject;

    /**
     * @var MetaReader
     */
    private $metaReader;

    public function __construct(TransferObjectInterface $transferObject)
    {
        $this->transferObject = $transferObject;
        $this->metaReader     = new MetaReader($transferObject);
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function hydrate(Request $request)
    {
        $this->data = $request->isMethod('GET') ? $request->query->all() : $this->getBody($request);

        $propertyAccessor = new PropertyAccessor();

        foreach ($this->metaReader->getProperties() as $propertyName => $annotation) {
            $value = $this->getValue($annotation->name);

            if (Property::TYPE_ARRAY === $annotation->type) {
                $value = $this->splitStringToArray($value);
            }

            if ($annotation->mapped) {
                $propertyAccessor->setValue($this->transferObject, $propertyName, $value);
            }
        }

        $this->runCallbacks($this->metaReader->getOnObjectHydratedAnnotations());
    }

    /**
     * @param array $callbacks
     *
     * @throws \Exception
     */
    private function runCallbacks(array $callbacks)
    {
        foreach ($callbacks as $event) {
            if (!empty($event->method)) {
                [$this->transferObject, $event->method]();
            } elseif (is_callable($event->callback)) {
                ($event->callback)($this->transferObject);
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

    public function runOnObjectValidCallbacks()
    {
        $this->runCallbacks($this->metaReader->getOnObjectValidAnnotations());
    }
}

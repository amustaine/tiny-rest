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

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var TypeCaster
     */
    private $typeCaster;

    public function __construct(TransferObjectInterface $transferObject)
    {
        $this->transferObject   = $transferObject;
        $this->metaReader       = new MetaReader($transferObject);
        $this->propertyAccessor = new PropertyAccessor();
        $this->typeCaster       = new TypeCaster();
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function hydrate(Request $request)
    {
        $this->data = $request->isMethod('GET') ? $request->query->all() : $this->getBody($request);


        foreach ($this->metaReader->getProperties() as $propertyName => $annotation) {
            if (!$this->hasValue($annotation->name)) {
                continue;
            }

            $value = $this->getValue($annotation->name);

            if ($annotation->mapped) {
                $this->propertyAccessor->setValue($this->transferObject, $propertyName, $value);
            }
        }

        $this->runCallbacks($this->metaReader->getOnObjectHydratedAnnotations());
    }

    public function castTypes()
    {
        foreach ($this->metaReader->getProperties() as $propertyName => $annotation) {
            if (!$annotation->type || !$annotation->mapped) {
                continue;
            }

            $value = $this->castType($annotation->type, $this->getValue($annotation->name));
            $this->propertyAccessor->setValue($this->transferObject, $propertyName, $value);
        }
    }

    /**
     * @param string $type
     * @param $value
     *
     * @return array|bool|\DateTime|null
     */
    private function castType(string $type, $value)
    {
        if (null === $value) {
            return null;
        }

        switch ($type) {
            case Property::TYPE_ARRAY :
                $value = $this->typeCaster->getArray($value);
                break;
            case Property::TYPE_BOOLEAN :
                $value = $this->typeCaster->getBoolean($value);
                break;
            case Property::TYPE_DATETIME :
                $value = $this->typeCaster->getDateTime($value);
                break;
            case Property::TYPE_INTEGER :
                $value = $this->typeCaster->getInteger($value);
                break;
            case Property::TYPE_FLOAT :
                $value = $this->typeCaster->getInteger($value);
                break;
            default :
                throw new \InvalidArgumentException(sprintf('Unknown type given: "%s"', $type));
        }

        return $value;
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
     * @return bool
     */
    public function hasValue(string $property) : bool
    {
        return array_key_exists($property, $this->data);
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

    public function runOnObjectValidCallbacks()
    {
        $this->runCallbacks($this->metaReader->getOnObjectValidAnnotations());
    }
}

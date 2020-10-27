<?php

/*
 * This file is part of the TinyRest package.
 *
 * (c) TinyRest <https://github.com/RuSS-B/tiny-rest>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TinyRest\Hydrator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

/**
 * @author Russ Balabanov <russ.developer@gmail.com>
 */
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

    public function __construct(object $transferObject)
    {
        $this->transferObject   = $transferObject;
        $this->metaReader       = new MetaReader($transferObject);
        $this->propertyAccessor = new PropertyAccessor();
        $this->typeCaster       = new TypeCaster();
    }

    public function handleRequest(Request $request)
    {
        $data = $request->isMethod('GET') ? $request->query->all() : $this->getBody($request);

        $this->hydrate($data);
    }

    /**
     * @param $data
     */
    public function hydrate($data): void
    {
        if ($data instanceof Request) {
            trigger_error('Passing Request object in hydrate() is deprecated and will be removed in version 2.0 use handleRequest() instead',
                E_USER_DEPRECATED);

            $this->handleRequest($data);
            return;
        }

        $this->data = $data;

        foreach ($this->metaReader->getProperties() as $propertyName => $annotation) {
            if (!$this->hasValue($annotation->name)) {
                continue;
            }

            $value = $this->getValue($annotation->name);

            if ($annotation->mapped) {
                if ($annotation->type) {
                    $value = $this->castType($annotation->type, $value, $annotation->extra);
                }

                $this->propertyAccessor->setValue($this->transferObject, $propertyName, $value);
            }
        }

        $this->runCallbacks($this->metaReader->getOnObjectHydratedAnnotations());
    }

    /**
     * @param string $type
     * @param $value
     * @param array $extra
     *
     * @return array|bool|\DateTime|null|object
     */
    private function castType(string $type, $value, array $extra = [])
    {
        if (null === $value) {
            return null;
        }

        switch ($type) {
            case Property::TYPE_STRING :
                $value = $this->typeCaster->getString($value);
                break;
            case Property::TYPE_ARRAY :
                $hasExtra = isset($extra[Property::COMMA_SEPARATED]);
                if (!$hasExtra || $extra[Property::COMMA_SEPARATED] !== false) {
                    $value = is_string($value) ? $this->typeCaster->getArray($value) : null;
                }
                break;
            case Property::TYPE_BOOLEAN :
                if (false === is_bool($value)) {
                    $value = is_string($value) ? $this->typeCaster->getBoolean($value) : null;
                }
                break;
            case Property::TYPE_DATETIME :
                $value = $this->typeCaster->getDateTime($value);
                break;
            case Property::TYPE_INTEGER :
                $value = $this->typeCaster->getInteger($value);
                break;
            case Property::TYPE_FLOAT :
                $value = $this->typeCaster->getFloat($value);
                break;
            default :
                if (class_exists($type)) {
                    $transferObject = new $type;
                    (new TransferObjectHydrator($transferObject))->hydrate($value);

                    $value = $transferObject;
                } else {
                    throw new \InvalidArgumentException(sprintf('Unknown type given: "%s"', $type));
                }
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
                ([$this->transferObject, $event->method])();
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
    public function hasValue(string $property): bool
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
    private function getBody(Request $request): array
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

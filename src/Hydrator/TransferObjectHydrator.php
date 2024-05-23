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

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

/**
 * @author Russ Balabanov <russ.developer@gmail.com>
 */
class TransferObjectHydrator
{
    private array                    $data = [];
    private MetaReader               $metaReader;
    private PropertyAccessor         $propertyAccessor;
    private TypeCaster               $typeCaster;

    public function __construct(private readonly TransferObjectInterface $transferObject)
    {
        $this->metaReader       = new MetaReader($transferObject);
        $this->propertyAccessor = new PropertyAccessor();
        $this->typeCaster       = new TypeCaster();
    }

    public static function payload(Request $request) : array
    {
        return $request->isMethod('GET') ? $request->query->all() : self::getBody($request);
    }

    public function handleRequest(Request $request) : void
    {
        $this->hydrate(self::payload($request));
    }

    public function hydrate(array|Request $data): void
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

    private function castType(string $type, $value, array $extra = []) : mixed
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
     * @throws InvalidArgumentException
     */
    private function runCallbacks(array $callbacks) : void
    {
        foreach ($callbacks as $event) {
            if (!empty($event->method)) {
                ([$this->transferObject, $event->method])();
            } elseif (is_callable($event->callback)) {
                ($event->callback)($this->transferObject);
            } else {
                throw new InvalidArgumentException('Invalid callback');
            }
        }
    }

    public function hasValue(string $property) : bool
    {
        return array_key_exists($property, $this->data);
    }

    private function getValue(string $property) : mixed
    {
        return $this->data[$property] ?? null;
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function getBody(Request $request) : array
    {
        if (!$request->getContent()) {
            return [];
        }

        $body = json_decode($request->getContent(), true);

        if (json_last_error() || null === $body) {
            throw new InvalidArgumentException('Invalid JSON');
        }

        return $body;
    }

    public function runOnObjectValidCallbacks() : void
    {
        $this->runCallbacks($this->metaReader->getOnObjectValidAnnotations());
    }
}

<?php

namespace TinyRest\Hydrator;

class ObjectMeta implements ObjectMetaInterface
{
    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $relations;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @var array|object
     */
    private $data;

    /**
     * @param array $properties
     * @param array $relations
     * @param array $mapping
     * @param array|object $data
     */
    public function __construct(array $properties, array $relations, array $mapping, $data)
    {
        $this->properties = $properties;
        $this->relations  = $relations;
        $this->mapping    = $mapping;
        $this->data       = $data;
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getRelations() : array
    {
        return $this->relations;
    }

    /**
     * @return array
     */
    public function getMapping() : array
    {
        return $this->mapping;
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }
}

<?php

namespace TinyRest\Hydrator;

class TypeCaster
{
    /**
     * @param string $value
     *
     * @return bool
     */
    public function getBoolean(string $value) : bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function getArray(string $value) : array
    {
        $data = [];

        $properties = explode(',', $value);
        foreach ($properties as $property) {
            $data[] = trim($property);
        }

        return $data;
    }

    /**
     * @param $value
     *
     * @return \DateTime|null
     */
    public function getDateTime($value) : ?\DateTime
    {
        $timestamp = strtotime($value);

        if (false === $timestamp) {
            return null;
        }

        return (new \DateTime())->setTimestamp($timestamp);
    }

    /**
     * @param $value
     *
     * @return int
     */
    public function getInteger($value) : int
    {
        return (int) $value;
    }

    /**
     * @param $value
     *
     * @return float
     */
    public function getFloat($value) : float
    {
        return (float) $value;
    }
}

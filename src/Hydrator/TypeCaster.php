<?php

namespace TinyRest\Hydrator;

use DateTime;

class TypeCaster
{
    public function getBoolean(string $value) : bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getArray(string $value) : array
    {
        $data = [];

        if (empty($value)) {
            return [];
        }

        $properties = explode(',', $value);
        foreach ($properties as $property) {
            $data[] = trim($property);
        }

        return $data;
    }

    public function getDateTime(mixed $value) : ?DateTime
    {
        $timestamp = strtotime($value);

        if (false === $timestamp) {
            return null;
        }

        return (new DateTime())->setTimestamp($timestamp);
    }

    public function getInteger(mixed $value) : int
    {
        return (int) $value;
    }

    public function getFloat(mixed $value) : float
    {
        return (float) $value;
    }

    public function getString(mixed $value) : string
    {
        return (string) $value;
    }
}

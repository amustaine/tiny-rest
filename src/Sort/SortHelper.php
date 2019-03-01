<?php

namespace TinyRest\Sort;

class SortHelper
{
    /**
     * @param array $sortFields
     * @param null|string $value
     *
     * @return bool
     */
    public static function isAllowedToSort(array $sortFields, ?string $value) : bool
    {
        if (!$value) {
            return false;
        }

        foreach ($sortFields as $item) {
            if ($item instanceof SortField && $item->getFieldByAlias($value)) {
                return true;
            } elseif (in_array($value, $sortFields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $sortFields
     * @param string $value
     *
     * @return string
     */
    public static function getSortField(array $sortFields, string $value) : string
    {
        foreach ($sortFields as $sortField) {
            if ($sortField instanceof SortField) {
                if ($field = $sortField->getFieldByAlias($value)) {
                    return $field;
                }
            }
        }

        return $value;
    }

    /**
     * @param string|null $dir
     *
     * @return string
     */
    public static function getSortDir(?string $dir) : string
    {
        return strtolower($dir) === 'desc' ? 'desc' : 'asc';
    }
}

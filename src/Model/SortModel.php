<?php

namespace TinyRest\Model;

use Symfony\Component\HttpFoundation\Request;
use TinyRest\Sort\SortField;

class SortModel
{
    /**
     * @var string
     */
    private $sortDir;

    /**
     * @var string|null
     */
    private $sort;

    /**
     * @var string|null
     */
    private $field;

    public static function createFromRequest(Request $request, array $sortFields): self
    {
        $sort = $request->query->get('sort');
        $dir  = $request->query->get('sortDir');

        return new self($sort, $dir, $sortFields);
    }

    public function __construct(?string $sort, ?string $dir, array $rawSortFields)
    {
        $sortFields = $this->handleSortFields($rawSortFields);

        if ($this->isAllowedToSort($sortFields, $sort)) {
            $this->sort = $sort;
            $this->field = $this->getSortField($sortFields, $sort);
        }

        $this->setDir($dir);
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function getSortDir(): string
    {
        return $this->sortDir;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    private function setDir(?string $dir)
    {
        $this->sortDir = strtolower($dir ?? 'asc') === 'desc' ? 'desc' : 'asc';
    }

    protected function handleSortFields(array $rawSortFields)
    {
        $sortFields = [];

        foreach ($rawSortFields as $key => $value) {
            if (!$value instanceof SortField) {
                if (is_numeric($key)) {
                    $key = $value;
                }
                $sortFields[] = $this->toSortField($key, $value);
            } else {
                $sortFields[] = $value;
            }
        }

        return $sortFields;
    }

    private function toSortField(string $field, ?string $alias)
    {
        if (!$alias) {
            $alias = $field;
        }

        return new SortField($field, $alias);
    }

    private function isAllowedToSort(array $sortFields, ?string $value): bool
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

    private function getSortField(array $sortFields, string $value): string
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
}

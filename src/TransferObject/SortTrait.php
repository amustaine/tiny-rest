<?php

namespace TinyRest\TransferObject;

use TinyRest\Annotations\Property;

trait SortTrait
{
    /**
     * @var string|null
     *
     * @Property()
     */
    private $sort;

    /**
     * @var string|null
     *
     * @Property()
     */
    private $sortDir;

    /**
     * @return string|null
     */
    public function getSort() : ?string
    {
        return $this->sort;
    }

    /**
     * @param string|null $sort
     */
    public function setSort(?string $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string|null
     */
    public function getSortDir() : ?string
    {
        return $this->sortDir;
    }

    /**
     * @param string|null $sortDir
     */
    public function setSortDir(?string $sortDir)
    {
        $this->sortDir = $sortDir;
    }

    /**
     * @return bool
     */
    public function isAllowedToSort() : bool
    {
        return $this->getSort() && in_array($this->getSort(), $this->getAllowedToSort());
    }

}

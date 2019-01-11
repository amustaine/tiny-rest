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
    private $dir;

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
    public function getDir() : ?string
    {
        return $this->dir;
    }

    /**
     * @param string|null $dir
     */
    public function setDir(?string $dir)
    {
        $this->dir = $dir;
    }
}

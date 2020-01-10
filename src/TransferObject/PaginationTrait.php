<?php

namespace TinyRest\TransferObject;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;

/**
 * @deprecated since 1.3, will be removed in 2.0
 */
trait PaginationTrait
{
    /**
     * @Assert\Range(min="1")
     * @Property(type="integer")
     */
    private $page;

    /**
     * @Assert\Range(min="0")
     * @Assert\Regex("/\d+/")
     * @Property(type="integer")
     */
    private $pageSize;

    public function getPage() : int
    {
        return $this->page ?: 1;
    }

    public function getPageSize() : int
    {
        return $this->pageSize ?: 20;
    }

    public function setPage(?int $page)
    {
        $this->page = $page;
    }

    public function setPageSize(?int $pageSize)
    {
        $this->pageSize = $pageSize;
    }
}

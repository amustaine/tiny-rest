<?php

namespace TinyRest\TransferObject;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;

trait PaginationTrait
{
    /**
     * @Assert\Range(min="1")
     * @Property()
     */
    private $page;

    /**
     * @Assert\Regex("/\d+/")
     * @Property()
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

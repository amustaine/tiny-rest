<?php

namespace TinyRest\ListCollection;

class Collection
{
    /**
     * @var int
     */
    private $total;

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->total = count($data);
        $this->data  = $data;
    }

    /**
     * @return int
     */
    public function getTotal() : int
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }
}

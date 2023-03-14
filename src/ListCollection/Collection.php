<?php

namespace TinyRest\ListCollection;

class Collection
{
    private int $total;

    public function __construct(private readonly array $data)
    {
        $this->total = count($data);
    }

    public function getTotal() : int
    {
        return $this->total;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function normalize() : array
    {
        return [
            'total'   => $this->getTotal(),
            'data'    => $this->getData()
        ];
    }
}

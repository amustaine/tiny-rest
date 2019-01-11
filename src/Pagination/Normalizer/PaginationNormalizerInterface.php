<?php

namespace TinyRest\Pagination\Normalizer;

interface PaginationNormalizerInterface
{
    public function normalize($item) : array;
}

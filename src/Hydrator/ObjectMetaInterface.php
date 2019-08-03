<?php

namespace TinyRest\Hydrator;

interface ObjectMetaInterface
{
    public function getProperties() : array;

    public function getRelations() : array;

    public function getMapping() : array;
}

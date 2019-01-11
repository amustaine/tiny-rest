<?php

namespace TinyRest\Provider;

use TinyRest\TransferObject\TransferObjectInterface;

abstract class ArrayProvider implements ProviderInterface
{
    /**
     * @param TransferObjectInterface|null $transferObject
     *
     * @return array
     */
    abstract public function getData(TransferObjectInterface $transferObject) : array;
}

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
    abstract public function provide(TransferObjectInterface $transferObject) : array;

    public function toArray(TransferObjectInterface $transferObject)
    {
        return $this->provide($transferObject);
    }
}

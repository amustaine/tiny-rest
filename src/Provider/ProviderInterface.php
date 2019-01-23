<?php

namespace TinyRest\Provider;

use TinyRest\TransferObject\TransferObjectInterface;

interface ProviderInterface
{
    /**
     * @param TransferObjectInterface $transferObject
     */
    public function provide(TransferObjectInterface $transferObject);

    /**
     * @param TransferObjectInterface $transferObject
     */
    public function toArray(TransferObjectInterface $transferObject);
}

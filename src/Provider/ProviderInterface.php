<?php

namespace TinyRest\Provider;

use TinyRest\TransferObject\TransferObjectInterface;

interface ProviderInterface
{
    /**
     * @param TransferObjectInterface $transferObject
     */
    public function getData(TransferObjectInterface $transferObject);
}

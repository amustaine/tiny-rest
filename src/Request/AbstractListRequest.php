<?php

namespace TinyRest\Request;

use TinyRest\DataProvider\ProviderInterface;

abstract class AbstractListRequest extends AbstractRequest
{
    abstract public function getDataProvider() : ProviderInterface;
}

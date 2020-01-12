<?php

namespace TinyRest\TransferObject;

/**
 * @deprecated since 1.3 will be removed in 2.0
 */
interface SortableListTransferObjectInterface
{
    public function getSort() : ?string;

    public function getSortDir() : ?string;

    public function getAllowedToSort() : array;
}

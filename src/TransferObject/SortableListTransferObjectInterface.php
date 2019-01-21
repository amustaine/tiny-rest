<?php

namespace TinyRest\TransferObject;

interface SortableListTransferObjectInterface
{
    public function getSort() : ?string;

    public function getSortDir() : ?string;

    public function getAllowedToSort() : array;

    public function isAllowedToSort() : bool;
}

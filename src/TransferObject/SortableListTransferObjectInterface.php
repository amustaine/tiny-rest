<?php

namespace TinyRest\TransferObject;

interface SortableListTransferObjectInterface
{
    public function getSort() : ?string ;

    public function getDir() : ?string;

    public function getAllowedToSort() : array;
}

<?php

namespace TinyRest\TransferObject;

interface PaginatedListTransferObjectInterface extends ListInterface
{
    /**
     * @return int
     */
    public function getPage() : int;

    /**
     * @return int
     */
    public function getPageSize() : int;
}

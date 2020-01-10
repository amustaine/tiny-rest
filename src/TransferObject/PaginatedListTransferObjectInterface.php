<?php

namespace TinyRest\TransferObject;

/**
 * @deprecated since 1.3 will be removed in 2.0
 */
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

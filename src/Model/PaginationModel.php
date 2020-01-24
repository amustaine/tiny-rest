<?php

namespace TinyRest\Model;

use Symfony\Component\HttpFoundation\Request;

class PaginationModel
{
    const DEFAULT_PAGE_SIZE = 20;

    private $page;

    private $pageSize;

    public static function createFromRequest(Request $request): self
    {
        $page     = (int)$request->query->get('page', 1);
        $pageSize = (int)$request->query->get('pageSize', self::defaultPageSize());

        return new self($page, $pageSize);
    }

    public function __construct(int $page, ?int $pageSize)
    {
        $this->page      = $this->validatePage($page);
        $this->pageSize  = null === $pageSize ? self::defaultPageSize() : $pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    private function validatePage(int $page): int
    {
        if ($page < 1) {
            return 1;
        }

        return $page;
    }

    private static function defaultPageSize(): int
    {
        return (int)getenv('TINYREST_PAGE_SIZE') ?: self::DEFAULT_PAGE_SIZE;
    }
}

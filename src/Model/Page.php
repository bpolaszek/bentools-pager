<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;

final class Page implements PageInterface
{
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $nbItems;

    /**
     * Page constructor.
     * @param int $pageNumber
     * @param int $nbItems
     */
    public function __construct(int $pageNumber, int $nbItems = 0)
    {
        $this->pageNumber = max(0, $pageNumber);
        $this->nbItems = max(0, $nbItems);
    }

    /**
     * @inheritDoc
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->nbItems;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string) $this->pageNumber;
    }
}

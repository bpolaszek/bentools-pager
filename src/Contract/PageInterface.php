<?php

namespace BenTools\Pager\Contract;

interface PageInterface extends \Countable
{
    /**
     * The current page number.
     *
     * @return int
     */
    public function getPageNumber(): int;

    /**
     * Return how many items are to be found in the page.
     *
     * @return int
     */
    public function count(): int;

    /**
     * $this->getPageNumber() alias.
     *
     * @return string
     */
    public function __toString(): string;
}

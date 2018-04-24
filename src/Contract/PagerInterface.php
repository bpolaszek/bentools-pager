<?php

namespace BenTools\Pager\Contract;

interface PagerInterface extends \IteratorAggregate, \Countable
{

    /**
     * The number of items to show per page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int;

    /**
     * @param int $perPage
     * @return PagerInterface
     */
    public function setPerPage(int $perPage): PagerInterface;

    /**
     * Return the total number of items.
     *
     * @return int
     */
    public function getNumFound(): int;

    /**
     * @param int $numFound
     * @return PagerInterface
     */
    public function setNumFound(int $numFound): PagerInterface;

    /**
     * @param int $currentPageNumber
     * @return PagerInterface
     */
    public function setCurrentPageNumber(int $currentPageNumber): PagerInterface;

    /**
     * The current offset.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Return the total number of pages.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Loop over each page.
     *
     * @return PageInterface[]
     */
    public function getIterator(): iterable;

    /**
     * Return the current page.
     *
     * @return PageInterface
     */
    public function getCurrentPage(): PageInterface;

    /**
     * Return wether or not this page is the current one.
     *
     * @param PageInterface $page
     * @return bool
     */
    public function isCurrentPage(PageInterface $page): bool;

    /**
     * Return the previous page, if any.
     *
     * @return PageInterface|null
     */
    public function getPreviousPage(): ?PageInterface;

    /**
     * Return wether or not this page is the previous one.
     *
     * @param PageInterface $page
     * @return bool
     */
    public function isPreviousPage(PageInterface $page): bool;

    /**
     * Return the next page, if any.
     *
     * @return PageInterface|null
     */
    public function getNextPage(): ?PageInterface;

    /**
     * Return wether or not this page is the next one.
     *
     * @param PageInterface $page
     * @return bool
     */
    public function isNextPage(PageInterface $page): bool;

    /**
     * Return the first page.
     *
     * @return PageInterface
     */
    public function getFirstPage(): PageInterface;

    /**
     * Return wether or not this page is the first one.
     *
     * @param PageInterface $page
     * @return bool
     */
    public function isFirstPage(PageInterface $page): bool;

    /**
     * Return the last page.
     *
     * @return PageInterface
     */
    public function getLastPage(): PageInterface;

    /**
     * Return wether or not this page is the last one.
     *
     * @param PageInterface $page
     * @return bool
     */
    public function isLastPage(PageInterface $page): bool;

    /**
     * Return the page object of the given number.
     *
     * @param int $pageNumber
     * @return PageInterface
     */
    public function getPage(int $pageNumber): PageInterface;

    /**
     * @param PageInterface $page
     * @return int
     */
    public function getPageOffset(PageInterface $page): int;

    /**
     * Dump all Page objects into an array.
     *
     * @return array
     */
    public function asArray(): array;

    /**
     * @param PageInterface $page
     * @return string
     */
    public function getUrl(PageInterface $page): string;
}

<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerInterface;

final class DeltaPager implements PagerInterface
{
    /**
     * @var PagerInterface
     */
    private $pager;

    /**
     * @var int
     */
    private $delta;

    /**
     * @var bool
     */
    private $showFirstPage;

    /**
     * @var bool
     */
    private $showLastPage;

    /**
     * DeltaPager constructor.
     * @param PagerInterface $pager
     * @param int            $delta
     */
    public function __construct(PagerInterface $pager, int $delta = 0, bool $showFirstPage = true, bool $showLastPage = true)
    {
        $this->pager = $pager;
        $this->delta = $delta;
        $this->showFirstPage = $showFirstPage;
        $this->showLastPage = $showLastPage;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(): int
    {
        return $this->pager->getCurrentPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): iterable
    {
        $pager = $this->pager;
        foreach ($pager as $page) {
            if ($this->isValid($page)) {
                yield $page;
            }
        }
    }

    public function asArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * @param PageInterface $page
     * @return bool
     */
    private function isValid(PageInterface $page): bool
    {
        $pager = $this->pager;
        $currentPageNumber = $pager->getCurrentPage()->getPageNumber();
        $leftBorder = $pager->getFirstPage()->getPageNumber() + ($this->delta * 2);
        $rightBorder = $pager->getLastPage()->getPageNumber() - ($this->delta * 2);
        switch (true) {
            case true === $this->showFirstPage && $pager->isFirstPage($page):
            case true === $this->showLastPage && $pager->isLastPage($page):
            case true === $pager->isCurrentPage($page):
            case $page->getPageNumber() < $currentPageNumber && $page->getPageNumber() >= ($currentPageNumber - $this->delta):
            case $page->getPageNumber() > $currentPageNumber && $page->getPageNumber() <= ($currentPageNumber + $this->delta):
            case $currentPageNumber <= $leftBorder && $page->getPageNumber() <= $leftBorder:
            case $currentPageNumber >= $rightBorder && $page->getPageNumber() >= $rightBorder:
                return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(int $perPage): PagerInterface
    {
        $this->pager->setPerPage($perPage);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setNumFound(int $numFound): PagerInterface
    {
        $this->pager->setNumFound($numFound);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentPageNumber(int $currentPageNumber): PagerInterface
    {
        $this->pager->setCurrentPageNumber($currentPageNumber);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->pager->getPerPage();
    }

    /**
     * @inheritDoc
     */
    public function getNumFound(): int
    {
        return $this->pager->getNumFound();
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->pager->getOffset();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->pager->count();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): PageInterface
    {
        return $this->pager->getCurrentPage();
    }

    /**
     * @inheritDoc
     */
    public function isCurrentPage(PageInterface $page): bool
    {
        return $this->pager->isCurrentPage($page);
    }

    /**
     * @inheritDoc
     */
    public function getPreviousPage(): ?PageInterface
    {
        return $this->pager->getPreviousPage();
    }

    /**
     * @inheritDoc
     */
    public function isPreviousPage(PageInterface $page): bool
    {
        return $this->pager->isPreviousPage($page);
    }

    /**
     * @inheritDoc
     */
    public function getNextPage(): ?PageInterface
    {
        return $this->pager->getNextPage();
    }

    /**
     * @inheritDoc
     */
    public function isNextPage(PageInterface $page): bool
    {
        return $this->pager->isNextPage($page);
    }

    /**
     * @inheritDoc
     */
    public function getFirstPage(): PageInterface
    {
        return $this->pager->getFirstPage();
    }

    /**
     * @inheritDoc
     */
    public function isFirstPage(PageInterface $page): bool
    {
        return $this->pager->isFirstPage($page);
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): PageInterface
    {
        return $this->pager->getLastPage();
    }

    /**
     * @inheritDoc
     */
    public function isLastPage(PageInterface $page): bool
    {
        return $this->pager->isLastPage($page);
    }

    /**
     * @inheritDoc
     */
    public function getPage(int $pageNumber): PageInterface
    {
        return $this->pager->getPage($pageNumber);
    }

    /**
     * @inheritDoc
     */
    public function getPageOffset(PageInterface $page): int
    {
        return $this->pager->getPageOffset($page);
    }
}

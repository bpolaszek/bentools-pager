<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerInterface;

final class Pager implements PagerInterface
{
    /**
     * @var int
     */
    private $perPage;

    /**
     * @var int
     */
    private $currentPageNumber;

    /**
     * @var int
     */
    private $numFound;

    /**
     * Pager constructor.
     * @param int|null $perPage
     * @param int|null $currentPageNumber
     * @param int|null $numFound
     */
    public function __construct(int $perPage = null, int $currentPageNumber = null, int $numFound = null)
    {
        $this->perPage = $perPage ?? 0;
        $this->currentPageNumber = $currentPageNumber ?? 1;
        $this->numFound = $numFound ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        if (0 === $this->getPerPage()) {
            return 1;
        }
        return max(1, (int) ceil($this->getNumFound() / $this->getPerPage()));
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): iterable
    {
        for ($nbPages = count($this), $pageNumber = 1; $pageNumber <= $nbPages; $pageNumber++) {
            yield $this->getPage($pageNumber);
        }
    }

    /**
     * @inheritdoc
     */
    public function asArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * @inheritdoc
     */
    public function getPage(int $pageNumber): PageInterface
    {
        $nbPages = count($this);
        return new Page($pageNumber, $this->computeNbItems($pageNumber, $nbPages));
    }

    /**
     * @inheritdoc
     */
    public function setCurrentPageNumber(int $currentPageNumber): PagerInterface
    {
        $this->currentPageNumber = max(0, $currentPageNumber);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    /**
     * @inheritDoc
     */
    public function getNumFound(): int
    {
        return $this->numFound;
    }

    /**
     * @param int $numFound
     * @return Pager
     */
    public function setNumFound(int $numFound): PagerInterface
    {
        $this->numFound = max(0, $numFound);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     * @return Pager
     */
    public function setPerPage(int $perPage): PagerInterface
    {
        $this->perPage = max(0, $perPage);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return ($this->getCurrentPageNumber() - 1) * $this->getPerPage();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): PageInterface
    {
        return $this->getPage($this->getCurrentPageNumber());
    }

    /**
     * @inheritDoc
     */
    public function isCurrentPage(PageInterface $page): bool
    {
        return $page->getPageNumber() === $this->getCurrentPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getPreviousPage(): ?PageInterface
    {
        if (1 === $this->getCurrentPageNumber()) {
            return null;
        }
        return $this->getPage($this->getCurrentPageNumber() - 1);
    }

    /**
     * @inheritDoc
     */
    public function isPreviousPage(PageInterface $page): bool
    {
        return (-1 + $this->getCurrentPageNumber()) === $page->getPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getNextPage(): ?PageInterface
    {
        $nbPages = count($this);
        if ($nbPages === $this->getCurrentPageNumber()) {
            return null;
        }
        return $this->getPage($this->getCurrentPageNumber() + 1);
    }

    /**
     * @inheritDoc
     */
    public function isNextPage(PageInterface $page): bool
    {
        return (1 + $this->getCurrentPageNumber()) === $page->getPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getFirstPage(): PageInterface
    {
        return $this->getPage(1);
    }

    /**
     * @inheritDoc
     */
    public function isFirstPage(PageInterface $page): bool
    {
        return 1 === $page->getPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): PageInterface
    {
        return $this->getPage(count($this));
    }

    /**
     * @inheritDoc
     */
    public function isLastPage(PageInterface $page): bool
    {
        return count($this) === $page->getPageNumber();
    }

    /**
     * @inheritDoc
     */
    public function getPageOffset(PageInterface $page): int
    {
        return ($page->getPageNumber() - 1) * $this->getPerPage();
    }

    /**
     * @param int $pageNumber
     * @param int $nbPages
     * @return int
     */
    private function computeNbItems(int $pageNumber, int $nbPages): int
    {
        if ($pageNumber > $nbPages) {
            return 0;
        }

        if ($pageNumber === $nbPages) {
            return ($this->getPerPage() - (($pageNumber * $this->getPerPage()) - $this->getNumFound()));
        }

        return $this->getPerPage();
    }
}

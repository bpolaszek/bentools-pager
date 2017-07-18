<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;

class Pager implements PagerInterface
{
    /**
     * @var int
     */
    private $currentPageNumber;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var int
     */
    private $numFound;

    /**
     * @var PageUrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * Pager constructor.
     * @param int|null                     $perPage
     * @param PageUrlBuilderInterface|null $urlBuilder
     * @param int|null                     $numFound
     * @param int|null                     $currentPageNumber
     * @throws \RuntimeException
     */
    public function __construct(int $perPage = null, PageUrlBuilderInterface $urlBuilder = null, int $numFound = null, int $currentPageNumber = null)
    {
        $this->perPage = $perPage;
        $this->urlBuilder = $urlBuilder ?? PageParameterUrlBuilder::fromRequestUri();
        $this->numFound = $numFound;
        $this->currentPageNumber = $currentPageNumber;
    }

    /**
     * @param PageUrlBuilderInterface $urlBuilder
     * @return Pager
     */
    public function setUrlBuilder(PageUrlBuilderInterface $urlBuilder): Pager
    {
        $this->urlBuilder = $urlBuilder;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string
    {
        return $this->urlBuilder->buildUrl($this, $page);
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
    public function count(): int
    {
        return (int) ceil($this->getNumFound() / $this->getPerPage());
    }

    /**
     * @inheritDoc
     */
    public function getNumFound(): int
    {
        if (null === $this->numFound) {
            throw new PagerException(get_class($this) . '::$numFound has not been set.');
        }
        return $this->numFound;
    }

    /**
     * @param int $numFound
     * @return Pager
     */
    public function setNumFound(int $numFound): Pager
    {
        $this->numFound = $numFound;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        if (null === $this->perPage) {
            throw new PagerException(get_class($this) . '::$perPage has not been set.');
        }
        return $this->perPage;
    }

    /**
     * @param int $perPage
     * @return Pager
     */
    public function setPerPage(int $perPage): Pager
    {
        $this->perPage = $perPage;
        return $this;
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
    public function getPage(int $pageNumber): PageInterface
    {
        $nbPages = count($this);

        if ($pageNumber > $nbPages) {
            throw new PagerException(sprintf('Invalid page number: requested %d, got %d total pages.', $pageNumber, $nbPages));
        } elseif ($pageNumber === $nbPages) {
            $nbItems = ($this->getPerPage() - (($pageNumber * $this->getPerPage()) - $this->getNumFound()));
        } else {
            $nbItems = $this->getPerPage();
        }
        return new Page($pageNumber, $nbItems);
    }

    /**
     * @param int $currentPageNumber
     */
    public function setCurrentPageNumber(int $currentPageNumber)
    {
        $this->currentPageNumber = $currentPageNumber;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(): int
    {
        return null !== $this->currentPageNumber ? $this->currentPageNumber : $this->urlBuilder->getCurrentPageNumber();
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return iterator_to_array($this);
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
}

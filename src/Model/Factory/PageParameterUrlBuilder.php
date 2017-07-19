<?php

namespace BenTools\Pager\Model\Factory;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerFactoryInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use BenTools\Pager\Model\Pager;
use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;

class PageParameterUrlBuilder implements PageUrlBuilderInterface, PagerFactoryInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var string
     */
    private $pageNumberQueryParam;

    /**
     * PageUrlBuilder constructor.
     * @param string $baseUrl
     * @param int    $perPage
     * @param string $pageNumberQueryParam
     */
    public function __construct(string $baseUrl, int $perPage, string $pageNumberQueryParam = 'page')
    {
        $this->baseUrl = $baseUrl;
        $this->perPage = $perPage;
        $this->pageNumberQueryParam = $pageNumberQueryParam;
    }

    /**
     * Return the current page.
     *
     * @return int
     */
    private function getCurrentPageNumber(): int
    {
        $parsedQuery = parse_query((new Uri($this->baseUrl))->getQuery());
        return $parsedQuery[$this->pageNumberQueryParam] ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string
    {
        return (string) Uri::withQueryValue(new Uri($this->baseUrl), $this->pageNumberQueryParam, $page->getPageNumber());
    }

    /**
     * @param int    $perPage
     * @param string $pageNumberQueryParam
     * @return PageParameterUrlBuilder
     * @throws \RuntimeException
     */
    public static function fromRequestUri(int $perPage, string $pageNumberQueryParam = 'page'): self
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \RuntimeException('$_SERVER[\'REQUEST_URI\'] is not set.');
        }
        return new static($_SERVER['REQUEST_URI'], $perPage, $pageNumberQueryParam);
    }

    /**
     * @param int|null $numFound
     * @return PagerInterface
     */
    public function createPager(int $numFound = null): PagerInterface
    {
        $pager = new Pager($this->perPage, $this->getCurrentPageNumber(), $numFound);
        return $pager;
    }
}

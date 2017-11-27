<?php

namespace BenTools\Pager\Model\Factory;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerFactoryInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use BenTools\Pager\Model\Pager;
use function BenTools\QueryString\query_string;
use function BenTools\UriFactory\Helper\uri;

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
        return query_string(uri($this->baseUrl))->getParam($this->pageNumberQueryParam) ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string
    {
        $uri = uri($this->baseUrl);
        $qs = query_string($uri->getQuery());

        return (string) $uri->withQuery(
            (string) $qs->withParam(
                $this->pageNumberQueryParam,
                $page->getPageNumber()
            )
        );
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

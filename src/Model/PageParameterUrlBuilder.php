<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;

class PageParameterUrlBuilder implements PageUrlBuilderInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $queryParam;

    /**
     * PageUrlBuilder constructor.
     */
    public function __construct(string $baseUrl, string $queryParam = 'page')
    {
        $this->baseUrl = $baseUrl;
        $this->queryParam = $queryParam;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(): int
    {
        $parsedQuery = parse_query((new Uri($this->baseUrl))->getQuery());
        return $parsedQuery[$this->queryParam] ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string
    {
        return (string) Uri::withQueryValue(new Uri($this->baseUrl), $this->queryParam, $page->getPageNumber());
    }

    /**
     * @param string $queryParam
     * @return PageParameterUrlBuilder
     * @throws \RuntimeException
     */
    public static function fromRequestUri(string $queryParam = 'page'): self
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \RuntimeException('$_SERVER[\'REQUEST_URI\'] is not set.');
        }
        return new static($_SERVER['REQUEST_URI'], $queryParam);
    }
}

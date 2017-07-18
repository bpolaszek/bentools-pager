<?php

namespace BenTools\Pager\Model;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;

class OffsetParameterUrlBuilder implements PageUrlBuilderInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $offsetParam;

    /**
     * @var int
     */
    private $perPage;

    /**
     * PageUrlBuilder constructor.
     */
    public function __construct(string $baseUrl, string $offsetParam, int $perPage)
    {
        $this->baseUrl = $baseUrl;
        $this->offsetParam = $offsetParam;
        $this->perPage = $perPage;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageNumber(): int
    {
        $parsedQuery = parse_query((new Uri($this->baseUrl))->getQuery());
        $currentOffset = $parsedQuery[$this->offsetParam] ?? 0;
        return (int) floor($currentOffset / $this->perPage) + 1;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string
    {
        return (string) Uri::withQueryValue(new Uri($this->baseUrl), $this->offsetParam, $pager->getPageOffset($page));
    }

    /**
     * @param string $offsetParam
     * @param int    $perPage
     * @return OffsetParameterUrlBuilder
     * @throws \RuntimeException
     */
    public static function fromRequestUri(string $offsetParam, int $perPage): self
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \RuntimeException('$_SERVER[\'REQUEST_URI\'] is not set.');
        }
        return new static($_SERVER['REQUEST_URI'], $offsetParam, $perPage);
    }
}

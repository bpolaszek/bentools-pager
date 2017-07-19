<?php

namespace BenTools\Pager\Model\Factory;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerFactoryInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use BenTools\Pager\Model\Pager;
use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;

class OffsetParameterUrlBuilder implements PageUrlBuilderInterface, PagerFactoryInterface
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
    private $offsetParam;

    /**
     * PageUrlBuilder constructor.
     * @param string $baseUrl
     * @param int    $perPage
     * @param string $offsetParam
     */
    public function __construct(string $baseUrl, int $perPage, string $offsetParam)
    {
        $this->baseUrl = $baseUrl;
        $this->perPage = $perPage;
        $this->offsetParam = $offsetParam;
    }

    /**
     * @inheritDoc
     */
    private function getCurrentPageNumber(): int
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

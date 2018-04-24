<?php

namespace BenTools\Pager\Model\Factory;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerFactoryInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use BenTools\Pager\Model\Pager;
use function BenTools\QueryString\query_string;
use function BenTools\UriFactory\Helper\uri;

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
    private $offsetQueryParam;

    /**
     * PageUrlBuilder constructor.
     * @param string $baseUrl
     * @param int    $perPage
     * @param string $offsetQueryParam
     */
    public function __construct(string $baseUrl, int $perPage, string $offsetQueryParam)
    {
        $this->baseUrl = $baseUrl;
        $this->perPage = max(0, $perPage);
        $this->offsetQueryParam = $offsetQueryParam;
    }

    /**
     * @inheritDoc
     */
    private function getCurrentPageNumber(): int
    {
        $qs = query_string(uri($this->baseUrl));
        $currentOffset = (int) max(0, $qs->getParam($this->offsetQueryParam) ?? 0);

        return (int) floor($currentOffset / $this->perPage) + 1;
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
                $this->offsetQueryParam,
                $pager->getPageOffset($page)
            )
        );
    }

    /**
     * @param int    $perPage
     * @param string $offsetParam
     * @return OffsetParameterUrlBuilder
     * @throws \RuntimeException
     */
    public static function fromRequestUri(int $perPage, string $offsetParam): self
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \RuntimeException('$_SERVER[\'REQUEST_URI\'] is not set.');
        }
        return new static($_SERVER['REQUEST_URI'], $perPage, $offsetParam);
    }

    /**
     * @param int|null $numFound
     * @return PagerInterface
     */
    public function createPager(int $numFound = null): PagerInterface
    {
        $pager = new Pager($this->perPage, $this->getCurrentPageNumber(), $numFound, $this);
        return $pager;
    }
}

<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Contract\PagerFactoryInterface;
use BenTools\Pager\Contract\PagerInterface;
use BenTools\Pager\Contract\PageUrlBuilderInterface;
use BenTools\Pager\Model\DeltaPager;
use BenTools\Pager\Model\Factory\PageParameterUrlBuilder;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class DeltaPagerTest extends TestCase
{
    public function testDecorator()
    {
        $factory = new PageParameterUrlBuilder('http://localhost/?foo=bar&page=12', 50);
        $pager = $factory->createPager(5000);
        $decorated = new DeltaPager($pager);
        $decorated->setNumFound(10000);
        $decorated->setPerPage(20);
        $decorated->setCurrentPageNumber(15);

        $this->assertEquals(10000, $pager->getNumFound());
        $this->assertEquals(20, $pager->getPerPage());
        $this->assertEquals(15, $pager->getCurrentPage()->getPageNumber());

        $this->assertEquals($decorated->getPerPage(), $pager->getPerPage());
        $this->assertEquals($decorated->getNumFound(), $pager->getNumFound());
        $this->assertEquals($decorated->getOffset(), $pager->getOffset());
        $this->assertEquals($decorated->count(), $pager->count());
        $this->assertEquals($decorated->getCurrentPage(), $pager->getCurrentPage());
        $this->assertEquals($decorated->getPreviousPage(), $pager->getPreviousPage());
        $this->assertEquals($decorated->getNextPage(), $pager->getNextPage());
        $this->assertEquals($decorated->getFirstPage(), $pager->getFirstPage());
        $this->assertEquals($decorated->getLastPage(), $pager->getLastPage());

        $page = $pager->getPage(25);
        $this->assertEquals($decorated->isCurrentPage($page), $pager->isCurrentPage($page));
        $this->assertEquals($decorated->isPreviousPage($page), $pager->isPreviousPage($page));
        $this->assertEquals($decorated->isNextPage($page), $pager->isNextPage($page));
        $this->assertEquals($decorated->isFirstPage($page), $pager->isFirstPage($page));
        $this->assertEquals($decorated->isLastPage($page), $pager->isLastPage($page));

    }

    /**
     * @dataProvider getDeltaSets
     */
    public function testDelta(PagerInterface $pager, int $delta, bool $showFirstPage, bool $showLastPage, string $expected)
    {
        $decorated = new DeltaPager($pager, $delta, $showFirstPage, $showLastPage);
        $pages = array_map(function (PageInterface $page) {
            return (int) $page->getPageNumber();
        }, $decorated->asArray());
        $result = implode('|', $pages);
        $this->assertEquals($expected, $result);
    }

    public function getDeltaSets()
    {
        return [
            [
                $this->factory(10, 1, 500)->createPager(),
                0,
                true,
                true,
                '1|50'
            ],
            [
                $this->factory(10, 2, 500)->createPager(),
                0,
                true,
                true,
                '1|2|50'
            ],
            [
                $this->factory(10, 25, 500)->createPager(),
                0,
                true,
                true,
                '1|25|50'
            ],
            [
                $this->factory(10, 49, 500)->createPager(),
                0,
                true,
                true,
                '1|49|50'
            ],
            [
                $this->factory(10, 50, 500)->createPager(),
                0,
                true,
                true,
                '1|50'
            ],
            [
                $this->factory(10, 1, 500)->createPager(),
                0,
                false,
                false,
                '1'
            ],
            [
                $this->factory(10, 2, 500)->createPager(),
                0,
                false,
                false,
                '2'
            ],
            [
                $this->factory(10, 25, 500)->createPager(),
                0,
                false,
                false,
                '25'
            ],
            [
                $this->factory(10, 49, 500)->createPager(),
                0,
                false,
                false,
                '49'
            ],
            [
                $this->factory(10, 50, 500)->createPager(),
                0,
                false,
                false,
                '50'
            ],
            [
                $this->factory(10, 1, 500)->createPager(),
                2,
                true,
                true,
                '1|2|3|4|5|50'
            ],
            [
                $this->factory(10, 2, 500)->createPager(),
                2,
                true,
                true,
                '1|2|3|4|5|50'
            ],
            [
                $this->factory(10, 25, 500)->createPager(),
                2,
                true,
                true,
                '1|23|24|25|26|27|50'
            ],
            [
                $this->factory(10, 49, 500)->createPager(),
                2,
                true,
                true,
                '1|46|47|48|49|50'
            ],
            [
                $this->factory(10, 50, 500)->createPager(),
                2,
                true,
                true,
                '1|46|47|48|49|50'
            ],
        ];
    }

    /**
     * @param int $perPage
     * @param int $currentPageNumber
     * @param int $numFound
     * @return PagerFactoryInterface
     */
    protected function factory(int $perPage, int $currentPageNumber, int $numFound): PagerFactoryInterface
    {
        return new class($perPage, $currentPageNumber, $numFound) implements PagerFactoryInterface
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
             * @var PageUrlBuilderInterface
             */
            private $urlBuilder;

            public function __construct(int $perPage, int $currentPageNumber, int $numFound)
            {
                $this->perPage = $perPage;
                $this->currentPageNumber = $currentPageNumber;
                $this->numFound = $numFound;
                $this->urlBuilder = new PageParameterUrlBuilder('http://localhost/?foo=bar', $this->perPage);
            }

            public function createPager(): PagerInterface
            {
                return new Pager($this->perPage, $this->currentPageNumber, $this->numFound, $this->urlBuilder);
            }

        };
    }

}

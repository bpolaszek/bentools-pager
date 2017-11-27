<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Contract\PageInterface;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class PagerTest extends TestCase
{
    /**
     * @var Pager
     */
    private $pager;

    public function setUp()
    {
        $this->pager = new Pager(10, 1);
    }

    /**
     * @expectedException \BenTools\Pager\Model\Exception\PagerException
     * @expectedExceptionMessage numFound has not been set
     */
    public function testItThrowsErrorWhenSomethingIsMissing()
    {
        $this->pager->asArray();
    }

    public function testPager()
    {
        $pager = new Pager(10, 2);
        $this->assertEquals(2, $pager->getCurrentPageNumber());

        $pager->setNumFound(53);
        $this->assertCount(6, $pager);
        $this->assertCount(6, $pager->asArray());
    }

    public function testPagerWithNoItems()
    {
        $pager = $this->pager;
        $pager->setNumFound(0);
        $this->assertCount(1, $pager);
        $this->assertCount(1, $pager->asArray());
        $this->assertCount(0, $pager->getFirstPage());
    }

    public function testCurrentPage()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $currentPage = $pager->getCurrentPage();
        $this->assertEquals(1, $currentPage->getPageNumber());
        $this->assertTrue($pager->isCurrentPage($currentPage));
        $this->assertFalse($pager->isCurrentPage($pager->getPage(2)));

        $pager->setCurrentPageNumber(2);
        $currentPage = $pager->getCurrentPage();
        $this->assertEquals(2, $currentPage->getPageNumber());
        $this->assertTrue($pager->isCurrentPage($currentPage));
        $this->assertFalse($pager->isCurrentPage($pager->getPage(1)));
    }

    public function testPreviousPage()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $this->assertNull($pager->getPreviousPage());

        $pager->setCurrentPageNumber(3);
        $previousPage = $pager->getPreviousPage();
        $this->assertInstanceOf(PageInterface::class, $previousPage);
        $this->assertEquals(2, $previousPage->getPageNumber());
        $this->assertTrue($pager->isPreviousPage($previousPage));
    }

    public function testNextPage()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $nextPage = $pager->getNextPage();
        $this->assertInstanceOf(PageInterface::class, $nextPage);
        $this->assertEquals(2, $nextPage->getPageNumber());
        $this->assertTrue($pager->isNextPage($nextPage));

        $pager->setCurrentPageNumber(6);
        $this->assertNull($pager->getNextPage());
    }

    public function testFirstPage()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $firstPage = $pager->getFirstPage();
        $this->assertInstanceOf(PageInterface::class, $firstPage);
        $this->assertEquals(1, $firstPage->getPageNumber());
        $this->assertTrue($pager->isFirstPage($firstPage));

        $pager->setCurrentPageNumber(6);
        $firstPage = $pager->getFirstPage();
        $this->assertInstanceOf(PageInterface::class, $firstPage);
        $this->assertEquals(1, $firstPage->getPageNumber());
        $this->assertTrue($pager->isFirstPage($firstPage));
    }

    public function testLastPage()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $lastPage = $pager->getLastPage();
        $this->assertInstanceOf(PageInterface::class, $lastPage);
        $this->assertEquals(6, $lastPage->getPageNumber());
        $this->assertTrue($pager->isLastPage($lastPage));

        $pager->setCurrentPageNumber(6);
        $lastPage = $pager->getLastPage();
        $this->assertInstanceOf(PageInterface::class, $lastPage);
        $this->assertEquals(6, $lastPage->getPageNumber());
        $this->assertTrue($pager->isLastPage($lastPage));
    }

    public function testPageOffset()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertEquals(0, $pager->getPageOffset($pager->getFirstPage()));
        $this->assertEquals(10, $pager->getPageOffset($pager->getPage(2)));
        $this->assertEquals(50, $pager->getPageOffset($pager->getLastPage()));
    }

    public function testNbItems()
    {
        $pager = $this->pager;
        $pager->setNumFound(53);

        $this->assertCount(10, $pager->getFirstPage());
        $this->assertCount(10, $pager->getPage(2));
        $this->assertCount(10, $pager->getPage(3));
        $this->assertCount(10, $pager->getPage(4));
        $this->assertCount(10, $pager->getPage(5));
        $this->assertCount(3, $pager->getPage(6));
    }

    /**
     * @expectedException \BenTools\Pager\Model\Exception\PagerException
     */
    public function testThrowExceptionOnOutOfRange()
    {
        $pager = $this->pager;
        $pager->setNumFound(10);
        $pager->getPage(2);
    }

}

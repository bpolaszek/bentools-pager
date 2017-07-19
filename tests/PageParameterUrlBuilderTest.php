<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Model\Page;
use BenTools\Pager\Model\Factory\PageParameterUrlBuilder;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class PageParameterUrlBuilderTest extends TestCase
{

    public function testOnEmptyParam()
    {
        $baseUrl = 'http://www.example.org/foo#bar';
        $qb = new PageParameterUrlBuilder($baseUrl, 10, 'PageNumber');
        $pager = $qb->createPager(500);
        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $this->assertEquals('http://www.example.org/foo?PageNumber=3#bar', $qb->buildUrl($pager, $pager->getPage(3)));
    }

    public function testOnExistingParam()
    {
        $baseUrl = 'http://www.example.org/foo?PageNumber=5#bar';
        $qb = new PageParameterUrlBuilder($baseUrl, 10, 'PageNumber');
        $pager = $qb->createPager(500);
        $this->assertEquals(5, $pager->getCurrentPage()->getPageNumber());
        $this->assertEquals('http://www.example.org/foo?PageNumber=8#bar', $qb->buildUrl($pager, $pager->getPage(8)));
    }



    /**
     * @expectedException \RuntimeException
     */
    public function testFactoryFails()
    {
        unset($_SERVER['REQUEST_URI']);
        $qb = PageParameterUrlBuilder::fromRequestUri(50);
    }

    public function testFactorySucceeds()
    {
        $_SERVER['REQUEST_URI'] = 'http://foo.bar/?page_number=3';
        $qb = PageParameterUrlBuilder::fromRequestUri(50, 'page_number');
        $pager = $qb->createPager();
        $pager->setNumFound(500);
        $this->assertEquals(3, $pager->getCurrentPage()->getPageNumber());
    }

}

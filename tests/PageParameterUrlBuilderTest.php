<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Model\Page;
use BenTools\Pager\Model\PageParameterUrlBuilder;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class PageParameterUrlBuilderTest extends TestCase
{

    public function testOnEmptyParam()
    {
        $baseUrl = 'http://www.example.org/foo#bar';
        $qb = new PageParameterUrlBuilder($baseUrl, 'PageNumber');
        $this->assertEquals(1, $qb->getCurrentPageNumber());
        $this->assertEquals('http://www.example.org/foo?PageNumber=3#bar', $qb->buildUrl(new Pager(10, $qb), new Page(3, 10)));
    }

    public function testOnExistingParam()
    {
        $baseUrl = 'http://www.example.org/foo?PageNumber=5#bar';
        $qb = new PageParameterUrlBuilder($baseUrl, 'PageNumber');
        $this->assertEquals(5, $qb->getCurrentPageNumber());
        $this->assertEquals('http://www.example.org/foo?PageNumber=8#bar', $qb->buildUrl(new Pager(10, $qb), new Page(8, 10)));
    }

}

<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Model\Factory\OffsetParameterUrlBuilder;
use BenTools\Pager\Model\Page;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class OffsetParameterUrlBuilderTest extends TestCase
{

    public function testOnEmptyParam()
    {
        $baseUrl = 'http://www.example.org/foo#bar';
        $qb = new OffsetParameterUrlBuilder($baseUrl, 10, 'offset');
        $pager = $qb->createPager(500);
        $this->assertEquals(1, $pager->getCurrentPage()->getPageNumber());
        $this->assertEquals('http://www.example.org/foo?offset=20#bar', $qb->buildUrl($pager, $pager->getPage(3)));
    }

    public function testOnExistingParam()
    {
        $baseUrl = 'http://www.example.org/foo?offset=40#bar';
        $qb = new OffsetParameterUrlBuilder($baseUrl, 10, 'offset');
        $pager = $qb->createPager(500);
        $this->assertEquals(5, $pager->getCurrentPage()->getPageNumber());
        $this->assertEquals('http://www.example.org/foo?offset=70#bar', $qb->buildUrl($pager, $pager->getPage(8)));
    }

}

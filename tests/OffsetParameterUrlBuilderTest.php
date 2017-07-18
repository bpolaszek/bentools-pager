<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Model\OffsetParameterUrlBuilder;
use BenTools\Pager\Model\Page;
use BenTools\Pager\Model\Pager;
use PHPUnit\Framework\TestCase;

class OffsetParameterUrlBuilderTest extends TestCase
{

    public function testOnEmptyParam()
    {
        $baseUrl = 'http://www.example.org/foo#bar';
        $qb = new OffsetParameterUrlBuilder($baseUrl, 'offset', 10);
        $this->assertEquals(1, $qb->getCurrentPageNumber());
        $this->assertEquals('http://www.example.org/foo?offset=20#bar', $qb->buildUrl(new Pager(10, $qb), new Page(3, 10)));
    }

    public function testOnExistingParam()
    {
        $baseUrl = 'http://www.example.org/foo?offset=40#bar';
        $qb = new OffsetParameterUrlBuilder($baseUrl, 'offset', 10);
        $this->assertEquals(5, $qb->getCurrentPageNumber());
        $this->assertEquals('http://www.example.org/foo?offset=70#bar', $qb->buildUrl(new Pager(10, $qb), new Page(8, 10)));
    }

}

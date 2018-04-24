<?php

namespace BenTools\Pager\Tests;

use BenTools\Pager\Model\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{

    public function test()
    {
        $page = new Page(3, 15);
        $this->assertEquals(3, $page->getPageNumber());
        $this->assertEquals(15, count($page));
        $this->assertEquals('3', (string) $page);
    }

    public function testNegativePageNumber()
    {

        $page = new Page(-1, 15);
        $this->assertEquals(0, $page->getPageNumber());
    }

    public function testNegativeCount()
    {

        $page = new Page(-1, -1);
        $this->assertCount(0, $page);
    }

}

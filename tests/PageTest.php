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

    /**
     * @expectedException \BenTools\Pager\Model\Exception\PagerException
     */
    public function testCannotCount()
    {
        count(new Page(5));

    }

}

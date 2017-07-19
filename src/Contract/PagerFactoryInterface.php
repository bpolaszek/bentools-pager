<?php

namespace BenTools\Pager\Contract;

interface PagerFactoryInterface
{
    /**
     * @return PagerInterface
     */
    public function createPager(): PagerInterface;
}

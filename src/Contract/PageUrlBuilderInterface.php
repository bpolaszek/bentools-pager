<?php

namespace BenTools\Pager\Contract;

interface PageUrlBuilderInterface
{

    /**
     * @param PagerInterface $pager
     * @param PageInterface  $page
     * @return string
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string;
}

<?php

namespace BenTools\Pager\Contract;

interface PageUrlBuilderInterface
{

    /**
     * Return the current page.
     *
     * @return PageInterface
     */
    public function getCurrentPageNumber(): int;

    /**
     * @param PagerInterface $pager
     * @param PageInterface  $page
     * @return string
     */
    public function buildUrl(PagerInterface $pager, PageInterface $page): string;
}

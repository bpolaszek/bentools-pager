<?php

/**
 * MIT License (MIT)
 *
 * Copyright (c) 2014 Beno!t POLASZEK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * Pager
 * @author Beno!t POLASZEK - 2014
 */

namespace BenTools\Pager;
use BenTools\Url;

/**
 * Class Page
 *
 * @package BenTools\Pager
 */
class Page implements PageInterface, \ArrayAccess {

    protected   $iteration;
    protected   $isFirstPage;
    protected   $isLastPage;
    protected   $isCurrentPage;
    protected   $isPreviousPage;
    protected   $isNextPage;
    protected   $offset;
    protected   $resultCount;
    protected   $url;
    protected   $pager;

    public function __construct(Pager $pager, $iteration = 1) {
        $this->pager        =    $pager;
        $this->iteration    =    (int) $iteration;
        $this->populate();
    }

    /**
     * Populates Page's properties.
     */
    protected function populate() {
        $this->isFirstPage();
        $this->isLastPage();
        $this->isCurrentPage();
        $this->isPreviousPage();
        $this->isNextPage();
        $this->getOffset();
        $this->getResultCount();
        $this->getUrl();
    }

    /**
     * Returns the page's current offset
     * @return int
     */
    public function getOffset() {
        if (!$this->offset)
            $this->offset   =   ($this->getIndex() * $this->getPager()->getResultsPerPage());
        return $this->offset;
    }

    /**
     * Returns the page's current offset
     * @return int
     */
    public function getResultCount() {
        if (!$this->resultCount)
            $this->resultCount   =   $this->isLastPage() ? $this->getPager()->getTotalResultCount() - $this->getOffset() : $this->getPager()->getResultsPerPage();
        return $this->resultCount;
    }

    /**
     * If the page is the 1st one.
     * @return bool
     */
    public function isFirstPage() {
        if (!$this->isFirstPage)
            $this->isFirstPage    =    ($this->getIteration() == 1);
        return $this->isFirstPage;
    }

    /**
     * If the page is the last one.
     * @return bool
     */
    public function isLastPage() {
        if (!$this->isLastPage)
            $this->isLastPage    =    ($this->getIteration() == $this->pager->getNbPages());
        return $this->isLastPage;
    }

    /**
     * If the page is the current one.
     * @return bool
     */
    public function isCurrentPage() {
        if (!$this->isCurrentPage)
            $this->isCurrentPage    =    ($this->getIteration() == ($this->pager->getCurrentPageIteration()));
        return $this->isCurrentPage;
    }

    /**
     * If the page is the previous one.
     * @return bool
     */
    public function isPreviousPage() {
        if (!$this->isPreviousPage)
            $this->isPreviousPage    =    ($this->getIteration() == ($this->pager->getCurrentPageIteration() - 1));
        return $this->isPreviousPage;
    }

    /**
     * If the page is the next one.
     * @return bool
     */
    public function isNextPage() {
        if (!$this->isNextPage)
            $this->isNextPage    =    ($this->getIteration() == ($this->pager->getCurrentPageIteration() + 1));
        return $this->isNextPage;
    }

    /**
     * Get the current page number (0-based)
     * @return int
     */
    public function getIndex() {
        return $this->getIteration() - 1;
    }

    /**
     * Get the current page number
     * @return int
     */
    public function getIteration() {
        return $this->iteration;
    }

    /**
     * Returns the Pager object
     * @return \BenTools\Pager\Pager
     */
    public function getPager() {
        return $this->pager;
    }

    /**
     * Generates page Url
     * @return string
     */
    public function getUrl() {

        if ($this->pager->getRewriteString()) {
            if (preg_match('#' . sprintf($this->pager->getRewriteString(), '([0-9]+)') . '#', (string) $this->pager->getUrl()))
                $this->setUrl(new Url(preg_replace('#' . sprintf($this->pager->getRewriteString(), '([0-9]+)') . '#', sprintf($this->pager->getRewriteString(), $this->getIteration()), (string) $this->pager->getUrl())));
            else {
                $url        =   clone $this->getPager()->getUrl();
                $this->setUrl($url->appendToPath(sprintf($this->getPager()->getRewriteString(), $this->getIteration())));
            }

        }

        else
            $this->setUrl(Url::NewInstance($this->pager->getUrl())->setParam($this->pager->getQueryParam(), $this->getIteration()));

        return $this->url;
    }

    /**
     * @param Url $url
     * @return $this - Provides Fluent Interface
     */
    public function setUrl(Url $url) {
        $this->url = $url;
        return $this;
    }

    /**
     * String context
     * @return string
     */
    public function __toString() {
        return (string) $this->getIteration();
    }


    /**
     * @see \ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return is_callable([$this, 'get' . $offset]) || is_callable([$this, $offset]);
    }

    /**
     * @see \ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        if (is_callable([$this, 'get' . $offset]))
            return call_user_func([$this, 'get' . $offset]);

        elseif (is_callable([$this, $offset]))
            return call_user_func([$this, $offset]);

        else
            return null;
    }

    /**
     * @see \ArrayAccess::offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value) {
        return is_callable([$this, 'set' . $offset]) ? call_user_func([$this, 'set' . $offset], $value) : $this;
    }

    /**
     * @see \ArrayAccess::offsetUnset
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset) {
        return $this->offsetSet($offset, null);
    }
}
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

namespace   BenTools\Pager;
use BenTools\Url;

/**
 * Class Pager
 *
 * @package BenTools\Pager
 */
class Pager implements \IteratorAggregate, \Countable, \ArrayAccess, \JsonSerializable {

    const       DEFAULT_PAGE_CLASS      =   '\\BenTools\\Pager\\Page';
    const       DEFAULT_QUERY_PARAM     =   'page';
    const       DEFAULT_DELTA           =   1;

    /**
     * The class that will handle each page.
     * @var string
     */
    protected   $pageClass              =   self::DEFAULT_PAGE_CLASS;

    /**
     * @var int
     */
    protected   $resultsPerPage         =   0;

    /**
     * The total results count, all pages included.
     * @var int
     */
    protected   $totalResultCount       =   0;

    /**
     * Current result count
     * @var int
     */
    protected   $currentResultCount     =   0;

    /**
     * The Query param to check in the query string
     * @var string
     */
    protected   $queryParam             =   self::DEFAULT_QUERY_PARAM;

    /**
     * A print_f compatible string if the page information is in a rewrite string instead of the query string
     * @var
     */
    protected   $rewriteString          =   '';

    /**
     * The current page iteration
     * @var int
     */
    protected   $currentPageIteration;

    /**
     * How many pages to show on each side when too many pages are rendered
     * @var int
     */
    protected   $delta                  =   self::DEFAULT_DELTA;

    /**
     * @var int
     */
    protected   $nbPages                =   0;

    /**
     * The current offset (useful for a LIMIT offset, max in a SQL clause)
     * @var int
     */
    protected   $currentOffset          =   0;

    /**
     * The Url to begin with
     * @var Url
     */
    protected   $url;

    /**
     * The pages rendered by the object (if there's a delta and too many pages, not all pages will be shown)
     * @var array
     */
    protected   $pages                  =   [];

    public function __construct($url = null, $resultsPerPage = null, $totalResultCount = null, $queryParam = self::DEFAULT_QUERY_PARAM, $rewriteString = null, $currentPageIteration = null, $delta = self::DEFAULT_DELTA) {

        if ($url instanceof Url)
            $this->setUrl($url);

        elseif ($url)
            $this->setUrl(new Url($url));

        elseif (isset($_SERVER['REQUEST_URI']))
            $this->setUrl(new Url($_SERVER['REQUEST_URI']));

        else
            $this->setUrl(new Url('/'));

        $this   ->  setResultsPerPage($resultsPerPage)
                ->  setTotalResultCount($totalResultCount)
                ->  setQueryParam($queryParam)
                ->  setRewriteString($rewriteString)
                ->  setDelta($delta);

        $this   ->  getCurrentPageIteration();
    }



    /**
     * Sets the delta value when you have too much pages
     * Example, if you've got 16 pages, you're on page 6 and you want a delta of 2,
     * You may want yo output something like : 1 ... 4 5 6 7 8 ... 16
     *
     * If set to false, disables the delta
     *
     * @param mixed $value
     * @return Pager instance
     */
    public function setDelta($value) {
        $this->delta = $value === false ? false : (int) $value;
        return $this;
    }

    /**
     * Returns the current delta
     * @return int
     */
    public function getDelta() {
        return $this->delta;
    }

    /**
     * @param int $nbPages
     * @return $this - Provides Fluent Interface
     */
    public function setNbPages($nbPages) {
        $this->nbPages = $nbPages;
        return $this;
    }

    /**
     * Returns the number of pages
     *
     * @return int
     */
    public function getNbPages() {
        return (int) ceil($this->totalResultCount / $this->resultsPerPage);
    }

    /**
     * Returns the current page iteration, according to the url.
     * 1 by default.
     *
     * @return int
     */
    public function getCurrentPageIteration() {
        if (!$this->currentPageIteration) {
            if (!is_null($this->rewriteString) && preg_match('#' . sprintf($this->rewriteString, '([0-9]+)') . '#', (string) $this->url, $matches))
                $this->currentPageIteration = (int) ((isset($matches[1]) && $matches[1] > 0) ? $matches[1] : 1);
            else
                $this->currentPageIteration = ($this->url->getParam($this->queryParam)) ? (int) $this->url->getParam($this->queryParam) : 1;
        }
        return (int) $this->currentPageIteration;

    }

    /**
     * @param int $currentPageIteration
     * @return $this - Provides Fluent Interface
     */
    public function setCurrentPageIteration($currentPageIteration) {
        $this->currentPageIteration = (int) $currentPageIteration;
        return $this;
    }

    /**
     * @return PageInterface
     */
    public function getCurrentPage() {
        $pageIndex = $this->getCurrentPageIteration() - 1;
        return (isset($this->pages[$pageIndex])) ? $this->pages[$pageIndex] : $this->getPageInstance($this->getCurrentPageIteration());
    }

    /**
     * Returns the Page Instance of the 1st page.
     *
     * @return Page Instance
     */
    public function getFirstPage() {
        return $this->getPageInstance(1);
    }

    /**
     * Returns the Page Instance of the last page.
     *
     * @return Page Instance
     */
    public function getLastPage() {
        return $this->getPageInstance($this->getNbPages());
    }

    /**
     * Returns the Page Instance of the previous page.
     *
     * @return Page Instance
     */
    public function getPreviousPage() {
        if ($this->getCurrentPageIteration() - 1 >= 1)
            return $this->getPageInstance($this->getCurrentPageIteration() - 1);
        else
            return $this->getFirstPage();
    }

    /**
     * Returns the Page Instance of the next page.
     *
     * @return Page Instance
     */
    public function getNextPage() {
        if ($this->getCurrentPageIteration() + 1 <= $this->getNbPages())
            return $this->getPageInstance($this->getCurrentPageIteration() + 1);
        else
            return $this->getLastPage();
    }

    /**
     * Returns the index of the resultset
     *
     * @return int
     */
    public function getCurrentOffset() {
        return (int) ($this->getCurrentPageIteration() - 1) * $this->resultsPerPage;
    }

    /**
     * Generates each Page Instance
     *
     * @return $this
     * @throws PagerException
     */
    public function run() {

        if (!$this->resultsPerPage)
            throw new PagerException(get_called_class() . "::setResultsPerPage has to be called first.");
        if (!is_integer($this->totalResultCount))
            throw new PagerException(get_called_class() . "::setTotalResultCount has to be called first.");

        $this->pages        =   [];

        $this->setNbPages((int) ceil($this->getTotalResultCount() / $this->getResultsPerPage()));

        # Case of a delta, e.g. << 1 ... 6 [7] 8 ... 15 >>
        if ($this->getDelta()) {

            $p              =   0;

            # Add first page, current page and last page
            $this->pages[]  =   $this->getPageInstance(1);
            $this->pages[]  =   $this->getPageInstance($this->getCurrentPageIteration());
            $this->pages[]  =   $this->getPageInstance($this->getNbPages());

            # Calculate delta
            for ($i = 1; $i <= $this->getDelta(); $i++) {

                ####    SUPERIOR DELTA        ####
                # If the next page doesn't overtstep the last one
                if ($this->getCurrentPageIteration() + $i <= $this->getNbPages())
                    $this->pages[] = $this->getPageInstance($this->getCurrentPageIteration() + $i);

                # Otherwise, we put the remaining delta before, providing it doesn't overstep the 1st one
                elseif ($this->getCurrentPageIteration() - $i >= 1)
                    $this->pages[] = $this->getPageInstance($this->getCurrentPageIteration() - $i);

                ####    INFERIOR DELTA        ####
                # If the previous page doesn't overstep the 1st one
                if ($this->getCurrentPageIteration() - $i >= 1)
                    $this->pages[] = $this->getPageInstance($this->getCurrentPageIteration() - $i);

                # Otherwise, we put the remaining delta before, providing it doesn't overstep the last one
                elseif ($this->getCurrentPageIteration() + $i <= $this->getNbPages())
                    $this->pages[] = $this->getPageInstance($this->getCurrentPageIteration() + $i);

            }
        }
        else {
            for ($i = 1; $i <= $this->getNbPages(); $i++)
                $this->pages[]  =   $this->getPageInstance($i);
        }
        $this->pages = array_unique((array) $this->pages);
        sort($this->pages);
        $this->currentOffset = $this->getCurrentOffset();
        $this->setCurrentResultCount($this->getCurrentPage()->isLastPage() ? $this->getTotalResultCount() - $this->getCurrentOffset() : $this->getResultsPerPage());
        return $this;
    }

    /**
     * @return array
     */
    public function getPages() {
        return $this->pages;
    }

    /**
     * @return Url
     */
    public function getUrl() {
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
     * @return string
     */
    public function getPageClass() {
        return $this->pageClass;
    }

    /**
     * @param string $pageClass
     * @return $this - Provides Fluent Interface
     */
    public function setPageClass($pageClass) {
        $this->pageClass = $pageClass;
        return $this;
    }

    /**
     * @return int
     */
    public function getResultsPerPage() {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     * @return $this - Provides Fluent Interface
     */
    public function setResultsPerPage($resultsPerPage) {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalResultCount() {
        return $this->totalResultCount;
    }

    /**
     * @param int $totalResultCount
     * @return $this - Provides Fluent Interface
     */
    public function setTotalResultCount($totalResultCount) {
        $this->totalResultCount = $totalResultCount;
        return $this;
    }

    /**
     * Returns the number of results of the current page
     *
     * @return int
     */
    public function getCurrentResultCount() {
        $end = (int) $this->getCurrentOffset() + (int) $this->resultsPerPage;
        return ($end > $this->totalResultCount) ? $this->totalResultCount : $end;
    }

    /**
     * @param int $currentResultCount
     * @return $this - Provides Fluent Interface
     */
    public function setCurrentResultCount($currentResultCount) {
        $this->currentResultCount = $currentResultCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueryParam() {
        return $this->queryParam;
    }

    /**
     * @param string $queryParam
     * @return $this - Provides Fluent Interface
     */
    public function setQueryParam($queryParam) {
        $this->queryParam = $queryParam;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRewriteString() {
        return $this->rewriteString;
    }

    /**
     * @param mixed $rewriteString
     * @return $this - Provides Fluent Interface
     */
    public function setRewriteString($rewriteString) {
        $this->rewriteString = $rewriteString;
        return $this;
    }

    /**
     * Iterator interface implementation
     *
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayObject($this->getPages());
    }

    /**
     * Countable interface implementation
     *
     * @return int
     */
    public function count() {
        return $this->getNbPages();
    }

    /**
     * @param $iteration
     * @return PageInterface
     */
    public function getPageInstance($iteration) {
        $class  =   $this->pageClass;
        return new $class($this, (int) $iteration);
    }

    /**
     * @see \ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return (is_numeric($offset) && array_key_exists($offset, $this->getPages())) || is_callable([$this, 'get' . $offset]) || is_callable([$this, 'is' . $offset]);
    }

    /**
     * @see \ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {

        if (is_numeric($offset))
            return array_key_exists($offset, $this->getPages()) ? $this->getPages()[$offset] : null;

        elseif (is_callable([$this, 'get' . $offset]))
            return call_user_func([$this, 'get' . $offset]);

        elseif (is_callable([$this, 'is' . $offset]))
            return call_user_func([$this, 'is' . $offset]);

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

    /**
     * JsonSerializable implementation
     * @return array
     */
    public function jsonSerialize() {
        return [
            'count'                 =>  $this->getNbPages(),
            'perPage'               =>  $this->getResultsPerPage(),
            'currentPageIteration'  =>  $this->getCurrentPageIteration(),
            'currentOffset'         =>  $this->getCurrentOffset(),
            'pages'                 =>  $this->getPages(),
        ];
    }
}
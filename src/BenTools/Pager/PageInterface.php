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

/**
 * Interface PageInterface
 *
 * @package BenTools\Pager
 */
interface PageInterface {

    /**
     * If the page is the 1st one.
     *
     * @return bool
     */
    public function isFirstPage();

    /**
     * If the page is the last one.
     *
     * @return bool
     */
    public function isLastPage();

    /**
     * If the page is the current one.
     *
     * @return bool
     */
    public function isCurrentPage();

    /**
     * If the page is the previous one.
     *
     * @return bool
     */
    public function isPreviousPage();

    /**
     * If the page is the next one.
     *
     * @return bool
     */
    public function isNextPage();

    /**
     * Returns the result count of this page
     * @return int
     */
    public function getResultCount();

    /**
     * Get the current page number (0-based)
     *
     * @return int
     */
    public function getIndex();

    /**
     * Get the current page number (1-based)
     *
     * @return int
     */
    public function getIteration();

    /**
     * Returns the Pager object
     *
     * @return \BenTools\Pager\Pager
     */
    public function getPager();

    /**
     * Generates page Url
     *
     * @return string
     */
    public function getUrl();

    /**
     * String context
     *
     * @return string
     */
    public function __toString();
}
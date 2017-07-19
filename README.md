[![Latest Stable Version](https://poser.pugx.org/bentools/pager/v/stable)](https://packagist.org/packages/bentools/pager)
[![License](https://poser.pugx.org/bentools/pager/license)](https://packagist.org/packages/bentools/pager)
[![Build Status](https://img.shields.io/travis/bpolaszek/bentools-pager/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/bentools-pager)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/bentools-pager/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/bentools-pager?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/bpolaszek/bentools-pager.svg?style=flat-square)](https://scrutinizer-ci.com/g/bpolaszek/bentools-pager)
[![Total Downloads](https://poser.pugx.org/bentools/pager/downloads)](https://packagist.org/packages/bentools/pager)

bentools/pager
==============

PHP7.1+ - An OOP pager, the way it should be, with a KISS approach.

Usage
-----

You just need to provide 3 informations:

* The number of items per page
* The current page number (can be provided by factories reading the current Url)
* The total number of items.

```php
use BenTools\Pager\Model\Pager;

$perPage = 10;
$currentPageNumber = 1;
$numFound = 53;
$pager = new Pager($perPage, $currentPageNumber, $numFound);
foreach ($pager as $page) {
    // do stuff
}
```

Example
-------

```php
# http://localhost/?page_number=3

require_once __DIR__ . '/vendor/autoload.php';

use BenTools\Pager\Model\Factory\PageParameterUrlBuilder;

$perPage = 10;
$urlBuilder = PageParameterUrlBuilder::fromRequestUri($perPage, 'page_number');
$pager = $urlBuilder->createPager();
$pager->setNumFound(53);


printf('Total number of pages: %s' . PHP_EOL, count($pager));
printf('Current page number: %s' . PHP_EOL, $pager->getCurrentPage());

print PHP_EOL;

printf('First page number: %s' . PHP_EOL, $pager->getFirstPage());
printf('Previous page number: %s' . PHP_EOL, $pager->getPreviousPage());
printf('Next page number: %s' . PHP_EOL, $pager->getNextPage());
printf('Last page number: %s' . PHP_EOL, $pager->getLastPage());

print PHP_EOL;

foreach ($pager as $page) {
    printf('Page %s contains %d items. - Url: %s' . PHP_EOL, $page, count($page), $urlBuilder->buildUrl($pager, $page));
}
```

Output:
```
Total number of pages: 6
Current page number: 1

First page number: 1
Previous page number: 
Next page number: 2
Last page number: 6

Page 1 contains 10 items. - Url: /?page_number=1
Page 2 contains 10 items. - Url: /?page_number=2
Page 3 contains 10 items. - Url: /?page_number=3
Page 4 contains 10 items. - Url: /?page_number=4
Page 5 contains 10 items. - Url: /?page_number=5
Page 6 contains 3 items. - Url: /?page_number=6
```

Delta Management
----------------

When you have a huge number of pages, you can use the `DeltaPager` decorator to show only relevant pages.
```php
# http://localhost/?page=30

require_once __DIR__ . '/vendor/autoload.php';

use BenTools\Pager\Model\DeltaPager;
use BenTools\Pager\Model\Factory\PageParameterUrlBuilder;

$perPage = 10;
$pager = PageParameterUrlBuilder::fromRequestUri($perPage)->createPager();
$pager->setNumFound(500);


printf('Total number of pages: %s' . PHP_EOL, count($pager));
printf('Current page number: %s' . PHP_EOL, $pager->getCurrentPage());

print PHP_EOL;

printf('First page number: %s' . PHP_EOL, $pager->getFirstPage());
printf('Previous page number: %s' . PHP_EOL, $pager->getPreviousPage());
printf('Next page number: %s' . PHP_EOL, $pager->getNextPage());
printf('Last page number: %s' . PHP_EOL, $pager->getLastPage());

print PHP_EOL;

$previous = null;
$delta = 2;
foreach (new DeltaPager($pager, $delta) as $page) {
    if (null !== $previous && $previous->getPageNumber() != $page->getPageNumber() - 1) {
        print '...' . PHP_EOL;
    }
    printf('Page %s' . PHP_EOL, $page);
    $previous = $page;
}
```

Output:
```
Total number of pages: 50
Current page number: 30

First page number: 1
Previous page number: 29
Next page number: 31
Last page number: 50

Page 1
...
Page 28
Page 29
Page 30
Page 31
Page 32
...
Page 50
```

Installation
------------

> composer require bentools/pager

Tests
-----

> ./vendor/bin/phpunit
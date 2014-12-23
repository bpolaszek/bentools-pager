bentools-pager
==============
A simple Pager class with delta management

Example use
------------

```php
$pager      =   new Pager;
$pager      ->  setResultsPerPage(10);
$pager      ->  setTotalResultCount(755);
$pager      ->  run();

foreach ($pager AS $p => $page) {
    if ($p > 0 && $pager[$p-1]['iteration'] != $pager[$p]['iteration'] - 1)
        print ' ... ' . PHP_EOL;

    printf('<a href="%s">Page %s</a> ' . PHP_EOL, $page['url'], $page['iteration']);
}

/*
 * Outputs :
 *
 * <a href="/my/uri?page=1">Page 1</a>
 * <a href="/my/uri?page=2">Page 2</a>
 *  ...
 * <a href="/my/uri?page=76">Page 76</a>
 */
```

Miscellaneous (before run)
------------
```php
$pager      =   new Pager($uri_different_than_server_request_uri); // If the 1st argument is null, the pager instanciates on $_SERVER['REQUEST_URI']
$pager      ->  setQueryParam('PAGE_NUMBER'); // The pager will guess the current page number and generate the pages Uris thanks to the PAGE_NUMBER param in the query string
$pager      ->  setRewriteString('/Page-%s_10'); // The pager won't rely on the query string but will guess the current page number and generate the pages Uris according to the rewrite string  
$pager      ->  setCurrentPageIteration(16); // Force the current page number instead of guessing it
$pager      ->  setDelta(false); // Will generate all the pages from 1 to 76 in our example
$pager      ->  setDelta(2); // Will only generate pages 1, 14, 15, 16, 17, 18, 76 (2 pages around the current page + 1st + last page)
```

Miscellaneous (after run)
------------
```php
$pager      ->  getCurrentPage();   // Returns the corresponding Page object
$pager      ->  getFirstPage();     // Returns the corresponding Page object
$pager      ->  getLastPage();      // Returns the corresponding Page object
$pager      ->  getPreviousPage();  // Returns the corresponding Page object
$pager      ->  getNextPage();      // Returns the corresponding Page object
$pager      ->  getLastPage()
            ->  getResultCount();   // Returns how many elements will be displayed on this page, i.e. 5
$pager      ->  getLastPage()
            ->  getOffset();        // Returns the page's offset, i.e. 750
            
foreach ($pager AS $page)
    var_dump(   
                $page   ->  isFirstPage(),
                $page   ->  isLastPage(),
                $page   ->  isNextPage(),
                $page   ->  isPreviousPage()
            );
```

Installation
------------
Add the following line into your composer.json :

    {
        "require": {
            "bentools/pager": "dev-master"
        }
    }  
Enjoy.
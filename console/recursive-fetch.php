<?php

/**
 * Working on an experimental version of `wget --recursive` that operates like the
 * system in simple-fetch.php. That it is to say that http addresses that match a regex
 * are transparently proxied to a recorder, and https addresses that match a regex are
 * converted to http, have a special header injected, then are transparently proxied to the
 * same recorder.
 *
 * While we could do the saving locally, it is nice to proxy it, as that creates a good
 * level of separation between functions. The alternative is to build an API to call to
 * store a response against a specific URL.
 *
 * @todo If the proxy address is wrong, the script should emit an error
 * @todo Upgrade Spatie\Crawler from 1.3 to 2.1.x? (It looks like 1.3 does not take the
 * query string into account when differentiating URLs, see
 * https://github.com/spatie/crawler/issues/59).
 */

use Proximate\SimpleCrawler;

$rootPath = realpath(__DIR__ . '/..');
require_once $rootPath . '/vendor/autoload.php';

#$startUrl = 'http://ilovephp.jondh.me.uk/';
#$pathRegex = '#^/en/tutorial#';

$startUrl = 'https://blog.jondh.me.uk/';
$pathRegex = '#^/category#';

$crawler = new SimpleCrawler('localhost:8081');
$crawler->
    init()->
    crawl($startUrl, $pathRegex);

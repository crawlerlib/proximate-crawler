<?php

/**
 * A class for a Spatie\Crawler to observe the web crawler's operation
 */

namespace Proximate\SpatieCrawler;

use Spatie\Crawler\CrawlObserver;
use Spatie\Crawler\Url;

class Observer implements CrawlObserver
{
    use \Proximate\Logger;

    public function willCrawl(Url $url)
    {
    }

    public function hasBeenCrawled(Url $url, $response, Url $foundOnUrl = null)
    {
        // Add query string if it is present
        $address = $url->path();
        if ($query = $url->query)
        {
            $address .= '?' . $query;
        }

        $this->log(sprintf("Crawled URL: %s", $address));
    }

    public function finishedCrawling()
    {
    }
}

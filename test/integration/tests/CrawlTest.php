<?php

/**
 * Checks the crawler, on a real site, without using a proxy
 */

namespace Proximate\Test\Integration;

use halfer\SpiderlingUtils\NamespacedTestCase;
use Proximate\SimpleCrawler;

class CrawlTest extends NamespacedTestCase
{
    const URL_PREFIX = 'http://localhost:10000';

    /**
     * @driver simple
     */
    public function testSimpleSite()
    {
        // Need to ensure that having no proxy works fine
        // Also need to swap logger out for in-memory logger we can check result
        $crawler = new SimpleCrawler(null);
        $crawler->
            allowNullProxy()->
            init()->
            crawl(self::URL_PREFIX . '/site1/page1.html', '#.+#');
    }

    public function testQueryStringLinks()
    {
        $this->markTestIncomplete();
    }
}

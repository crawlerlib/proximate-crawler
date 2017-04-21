<?php

/**
 * Checks the crawler, on a real site, without using a proxy
 */

namespace Proximate\Test\Integration;

use halfer\SpiderlingUtils\NamespacedTestCase;

class CrawlTest extends NamespacedTestCase
{
    const URL_PREFIX = 'http://localhost:10000';

    /**
     * @driver simple
     */
    public function testSomething()
    {
        // @todo Swap this for a run of the crawler
        $text = $this->visit(self::URL_PREFIX . '/site1/page1.html')->text();
        $this->assertContains('Linky', $text);
    }
}

<?php

/**
 * Checks the crawler, on a real site, without using a proxy
 */

namespace Proximate\Test\Integration;

use halfer\SpiderlingUtils\NamespacedTestCase;
use Proximate\SimpleCrawler;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class CrawlTest extends NamespacedTestCase
{
    const URL_PREFIX = 'http://localhost:10000';

    /**
     * @driver simple
     */
    public function testSimpleSite()
    {
        $crawler = new TestCrawler(null);
        $crawler->
            allowNullProxy()->
            init()->
            crawl(self::URL_PREFIX . '/site1/page1.html', '#.+#');
        $this->assertEquals(
            [
                'Crawled URL: /site1/page1.html',
                'Crawled URL: /site1/page2.html',
                'Crawled URL: page3.html',
            ],
            $crawler->getCrawlLogs()
        );
    }

    public function testQueryStringLinks()
    {
        $this->markTestIncomplete();
    }
}

class TestCrawler extends SimpleCrawler
{
    protected $logHandler;

    /**
     * Replaces the log handler of the parent
     */
    public function initLogger()
    {
        $this->logger = new Logger('memory');
        $this->logHandler = new TestHandler();
        $this->getLogger()->pushHandler($this->getLogHandler());

        return $this;
    }

    public function getCrawlLogs()
    {
        $records = $this->getLogHandler()->getRecords();
        array_walk($records, function(&$item) {
            $item = $item['message'];
        });

        return $records;
    }

    /**
     * Gets the current log handler
     *
     * @return TestHandler
     */
    protected function getLogHandler()
    {
        return $this->logHandler;
    }
}

<?php

/**
 * Simple crawler system
 */

namespace Proximate;

// Namespaces for the injection of a Proximate tweaking device into Guzzle
use GuzzleHttp\HandlerStack;
use Proximate\Guzzle\ProxyMiddleware;

// Namespaces for the creation of a Guzzle client
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

// Namespaces for logging
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

// Namespaces for the creation and set-up of the crawler
use Spatie\Crawler\Crawler;
use Proximate\SpatieCrawler\Observer;
use Proximate\SpatieCrawler\Profile;

// Exceptions
use Proximate\Exception\Init as InitException;

class SimpleCrawler
{
    protected $proxyAddress;
    protected $logger;
    protected $client;
    protected $observer;
    protected $profile;

    public function __construct($proxyAddress)
    {
        $this->proxyAddress = $proxyAddress;
    }

    /**
     * Simple init method
     *
     * @return self
     */
    public function init()
    {
        return $this->
            initLogger()->
            initMiddleware()->
            initObserver();
    }

    public function initLogger()
    {
        $this->logger = new Logger('stdout');
        $this->getLogger()->pushHandler(new ErrorLogHandler());

        return $this;
    }

    public function initMiddleware()
    {
        $stack = HandlerStack::create();
        $proxyMiddleware = new ProxyMiddleware();
        if ($logger = $this->getLogger())
        {
            $proxyMiddleware->addLogger($logger);
        }
        $stack->push($proxyMiddleware->getMiddleware());

        // Create the HTTP client
        $this->client = new Client([
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::PROXY => $this->getProxyAddress(),
            'handler' => $stack,
        ]);

        return $this;
    }

    public function initObserver()
    {
        $this->observer = new Observer();
        if ($logger = $this->getLogger())
        {
            $this->getObserver()->addLogger($logger);
        }

        return $this;
    }

    public function initProfile($startUrl, $pathRegex)
    {
        $this->profile = new Profile($startUrl, $pathRegex);

        return $this;
    }

    /**
     * The crawler method to call after everything is set up
     *
     * @param string $baseUrl
     */
    public function crawl($baseUrl)
    {
        $t = microtime(true);
        $crawler = new Crawler($this->getClient(), 1);
        $crawler->
            setCrawlProfile($this->getProfile())->
            setCrawlObserver($this->getObserver())->
            startCrawling($baseUrl);
        $et = microtime(true) - $t;

        return $et;
    }

    protected function getProxyAddress()
    {
        if (!$this->proxyAddress)
        {
            throw new InitException("The proxy address is not set");
        }

        return $this->proxyAddress;
    }

    /**
     * Gets the currently set logger (or null if it is not set)
     *
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Gets the currently set HTTP client
     *
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client)
        {
            throw new InitException("The client instance is not set");
        }

        return $this->client;
    }

    protected function getProfile()
    {
        if (!$this->profile)
        {
            throw new InitException("The crawler profile is not set");
        }

        return $this->profile;
    }

    protected function getObserver()
    {
        if (!$this->observer)
        {
            throw new InitException("The crawler observer is not set");
        }

        return $this->observer;
    }
}

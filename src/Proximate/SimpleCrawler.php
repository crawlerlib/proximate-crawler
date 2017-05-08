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
use Monolog\Handler\StreamHandler;

// Namespaces for the creation and set-up of the crawler
use Spatie\Crawler\Crawler;
use Proximate\SpatieCrawler\Observer;
use Proximate\SpatieCrawler\Profile;

// Exceptions
use Proximate\Exception\Init as InitException;

class SimpleCrawler
{
    protected $proxyAddress;
    protected $allowNullProxy = false;
    protected $logger;
    protected $client;
    protected $observer;
    protected $profile;

    public function __construct($proxyAddress = null)
    {
        $this->proxyAddress = $proxyAddress;
    }

    public function allowNullProxy()
    {
        $this->allowNullProxy = true;

        return $this;
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
        $this->getLogger()->pushHandler(new StreamHandler("php://stdout"));

        return $this;
    }

    public function initMiddleware()
    {
        $stack = HandlerStack::create();
        $proxyMiddleware = new ProxyMiddleware();
        if ($logger = $this->getLogger())
        {
            $proxyMiddleware->setLogger($logger);
        }
        $stack->push($proxyMiddleware->getMiddleware());

        // The proxy is optional (though HTTPS sites won't work without it)
        $options = [
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => true,
            'handler' => $stack,
        ];
        if ($proxy = $this->getProxyAddress())
        {
            $options[RequestOptions::PROXY] = $proxy;
        }
        $this->client = new Client($options);

        return $this;
    }

    /**
     * Sets up an observer class for the Spatie crawler
     *
     * @return $this
     */
    public function initObserver()
    {
        $this->observer = new Observer();
        if ($logger = $this->getLogger())
        {
            $this->getObserver()->setLogger($logger);
        }

        return $this;
    }

    /**
     * Returns a Profile class for the Spatie Crawler
     */
    protected function initProfile($baseUrl, $pathRegex)
    {
        return new Profile($baseUrl, $pathRegex);
    }

    /**
     * The crawler method to call after everything is set up
     *
     * @todo Deriving a base url will fail to get a non-standard port number
     *
     * @param string $startUrl
     * @param string $pathRegex
     */
    public function crawl($startUrl, $pathRegex)
    {
        // Ensure the URL is relatively sensible
        $scheme = parse_url($startUrl, PHP_URL_SCHEME);
        $host = parse_url($startUrl, PHP_URL_HOST);
        if (!$scheme || !$host)
        {
            throw new InitException(
                sprintf("Could not parse `%s` into a domain name", $startUrl)
            );
        }

        // Get the host to ensure we don't hop to a new host accidentally
        $baseUrl = "{$scheme}://{$host}/";
        $profile = $this->initProfile($baseUrl, $pathRegex);

        $t = microtime(true);
        $crawler = new Crawler($this->getClient(), 1);
        $crawler->
            setCrawlProfile($profile)->
            setCrawlObserver($this->getObserver())->
            startCrawling($startUrl);
        $et = microtime(true) - $t;

        return $et;
    }

    protected function getProxyAddress()
    {
        if (!$this->allowNullProxy && !$this->proxyAddress)
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

    protected function getObserver()
    {
        if (!$this->observer)
        {
            throw new InitException("The crawler observer is not set");
        }

        return $this->observer;
    }
}

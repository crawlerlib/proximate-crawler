<?php

/**
 * A class for a Spatie\Crawler to guide the web crawler's operation
 */

namespace Proximate\SpatieCrawler;

use Spatie\Crawler\CrawlProfile;
use Spatie\Crawler\Url;
use Proximate\Exception\Init as InitException;

class Profile implements CrawlProfile
{
    use \Proximate\Logger;

    protected $baseUrl;
    protected $pathRegex;

    /**
     * Sets up some filtering settings for the crawler
     *
     * @param string $baseUrl e.g. "http://www.example.com"
     * @param string $pathRegex e.g. "#^/careers#"
     */
    public function __construct($baseUrl, $pathRegex)
    {
        // Do some validation before allowing the instantiation
        $this->validateRegex($pathRegex);

        $this->baseUrl = $baseUrl;
        $this->pathRegex = $pathRegex;
    }

    public function shouldCrawl(Url $url) : bool
    {
        $matchesRegex = $this->regexMatch($url);
        $matchesRoot = $this->startMatch($url);

        $shouldCrawl =
            $this->sameHost($url) &&
            ($matchesRegex || $matchesRoot);

        if ($shouldCrawl)
        {
            $this->log(
                sprintf("Should crawl %s\n", $url)
            );
        }

        return $shouldCrawl;
    }

    protected function sameHost(Url $url)
    {
        return parse_url($this->baseUrl, PHP_URL_HOST) === $url->host;
    }

    protected function startMatch(Url $url)
    {
        return ((string) $url) == $this->baseUrl;
    }

    protected function regexMatch(Url $url)
    {
        return preg_match($this->pathRegex, $url->path) === 1;
    }

    protected function validateRegex($pattern)
    {
        // Use error suppression to test if the regex is OK
        @preg_match($pattern, '');

        // ... and we test it here
        $lastError = error_get_last();
        $lastMessage = isset($lastError['message']) ? $lastError['message'] : '';
        $errorPrefix =  'preg_match(): ';
        if(strpos($lastMessage, $errorPrefix) === 0)
        {
            $message = substr($lastMessage, strlen($errorPrefix));
            throw new InitException($message);
        }
    }
}

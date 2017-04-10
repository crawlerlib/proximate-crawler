Proximate/Crawler
===

Introduction
---

This system contains an instance of Spatie/Crawler and glue classes to run it against a Proximate
proxy. Proximate is a PHP-based HTTP recording proxy, which is designed to be modular and
extensible. The core of Proximate is called Proximate/Requester, and
[can be found here](https://github.com/halfer/proximate-requester).

Proximate/Crawler is also a good example of how to get any Guzzle 6 system to talk to a Proximate
proxy. HTTP endpoints do not need this, but if you're wanting to play/record HTTPS endpoints,
you'll need the middleware.

Usage
---

See the example in `console/recursive-fetch.php`. To crawl a website, you'll need:

* A start URL
* A regular expression to match new paths against
* A running instance of a Proximate proxy

Of course, if the crawler cannot find any links in the start page that match the regex, it will
not crawl anything.

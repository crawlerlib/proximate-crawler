<?php

namespace Proximate\Test;

use halfer\SpiderlingUtils\NamespacedTestListener as BaseTestListener;
use halfer\SpiderlingUtils\Server;

/**
 * Listener to turn server on for integration tests
 */

class TestListener extends BaseTestListener
{
    /**
	 * Specifies which suites and namespaces to respond to
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function switchOnBySuiteName($name)
	{
		return
			($name == 'integration') ||
			(strpos($name, 'Proximate\\Test\\Integration\\') !== false);
	}

	public function setupServers()
	{
		$testRoot = $this->getTestRoot();
		$server = new Server($testRoot);
        $server->setServerUri('127.0.0.1:10000');

		$this->addServer($server);
	}

    /**
     * Adds some server settling-down time
     *
     * (I could implement a check URL, but this seems to be just fine).
     *
     * @param Server $server
     */
    protected function checkServer(Server $server)
	{
        sleep(1);
    }

	protected function getTestRoot()
	{
		return realpath(__DIR__ . '/../public');
	}
}

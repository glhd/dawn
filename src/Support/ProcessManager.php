<?php

namespace Glhd\Dawn\Support;

use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Browser\SeleniumDriverProcess;
use Glhd\Dawn\Http\WebServerBroker;

class ProcessManager
{
	public function __construct(
		public readonly RemoteWebDriverBroker $remote_web_driver,
		public readonly WebServerBroker $web_server,
	) {
	}
	
	public function stop(): void
	{
		$this->web_server->stop();
		$this->remote_web_driver->stop();
	}
}

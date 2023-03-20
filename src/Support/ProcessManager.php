<?php

namespace Glhd\Dawn\Support;

use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Http\WebServerBroker;
use Illuminate\Container\Container;

class ProcessManager
{
	protected static ?self $instance = null; 
	
	public static function getInstance(): static
	{
		return static::$instance ??= Container::getInstance()->make(static::class);
	}
	
	public static function clearInstance(): void
	{
		static::$instance = null;
		
		Container::getInstance()->forgetInstance(static::class);
	}
	
	public function __construct(
		public RemoteWebDriverBroker $remote_web_driver,
		public WebServerBroker $web_server,
	) {
	}
	
	public function stop(): void
	{
		$this->web_server->stop();
		$this->remote_web_driver->stop();
	}
}

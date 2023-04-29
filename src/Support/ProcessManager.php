<?php

namespace Glhd\Dawn\Support;

use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Http\WebServerBroker;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;

class ProcessManager
{
	protected static ?self $instance = null;
	
	public RemoteWebDriverBroker $remote_web_driver;
	
	public WebServerBroker $web_server;
	
	public static function getInstance(array $config): static
	{
		// If we're fetching an instance and the configuration has changed dynamically
		// for some reason, we'll stop everything and create a new instance.
		if (static::$instance && static::$instance->config !== $config) {
			static::$instance->stop();
			static::$instance = null;
		}
		
		return static::$instance ??= new static($config);
	}
	
	public static function clearInstance(): void
	{
		static::$instance = null;
		
		Container::getInstance()->forgetInstance(static::class);
	}
	
	public function __construct(
		public readonly array $config
	) {
		$this->remote_web_driver = new RemoteWebDriverBroker(
			url: $this->config('dawn.browser_url', 'http://localhost:9515')
		);
		
		$this->web_server = new WebServerBroker(
			host: $this->config('dawn.server_host', '127.0.0.1'),
			port: $this->config('dawn.server_port') ?? $this->findOpenPort(),
		);
	}
	
	public function stop(): void
	{
		$this->web_server->stop();
		$this->remote_web_driver->stop();
	}
	
	protected function config(string $key, $default = null): mixed
	{
		return Arr::get($this->config, $key, $default);
	}
	
	protected function findOpenPort(): int
	{
		$sock = socket_create_listen(0);
		
		socket_getsockname($sock, $addr, $port);
		socket_close($sock);
		
		return $port;
	}
}

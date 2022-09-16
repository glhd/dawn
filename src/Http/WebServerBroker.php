<?php

namespace Glhd\Dawn\Http;

use Glhd\Dawn\Support\Broker;
use React\EventLoop\LoopInterface;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class WebServerBroker extends Broker
{
	public function __construct(
		public readonly string $host,
		public readonly int $port,
		?LoopInterface $loop = null,
		?InputStream $stdin = null,
	) {
		parent::__construct($loop, $stdin);
	}
	
	public function url(): string
	{
		/** @noinspection HttpUrlsUsage */
		return "http://{$this->host}:{$this->port}";
	}
	
	public function stop(): void
	{
		$this->debug('Stopping web server');
		
		$this->flushIncomingMessageStream();
		
		$this->process->signal(SIGTERM);
	}
	
	protected function startBackgroundProcess(InputStream $stdin): Process
	{
		$process = $this->artisan(['dawn:serve', $this->host, $this->port, public_path()], $stdin);
		
		register_shutdown_function(fn() => $process->stop(1, SIGKILL));
		
		$process->start();
		
		return $process;
	}
}

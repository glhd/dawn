<?php

namespace Glhd\Dawn\Browser;

use Glhd\Dawn\IO\Command;
use Glhd\Dawn\Support\Broker;
use Glhd\Dawn\Support\ResponseQueue;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class RemoteWebDriverBroker extends Broker
{
	protected ResponseQueue $queue;
	
	public function __construct(
		public readonly string $url,
		?LoopInterface $loop = null,
		?InputStream $stdin = null,
	) {
		$loop ??= Loop::get();
		
		parent::__construct($loop, $stdin);
		
		$this->queue = new ResponseQueue($loop);
	}
	
	public function addToResponseQueue(string $request_id, $response): static
	{
		$this->queue->push($request_id, $response);
		
		return $this;
	}
	
	public function stop(): void
	{
		$this->debug('Stopping remote web driver.');
		
		$this->process->signal(SIGTERM);
	}
	
	public function sendCommandAndWaitForResponse(Command $command): mixed
	{
		$this->sendCommand($command);
		
		return $this->queue->waitForResponse($command->id);
	}
	
	protected function startBackgroundProcess(InputStream $stdin): Process
	{
		$process = $this->artisan(['dawn:drive', $this->url], $stdin);
		
		register_shutdown_function(fn() => $process->stop(1, SIGKILL));
		
		$process->start();
		
		return $process;
	}
}

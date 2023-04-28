<?php

namespace Glhd\Dawn\Support;

use ErrorException;
use Glhd\Dawn\Concerns\SendsAndReceivesCommands;
use Glhd\Dawn\IO\Command;
use Glhd\Dawn\IO\CommandIO;
use Illuminate\Support\Facades\App;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Stream\DuplexStreamInterface;
use React\Stream\ThroughStream;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

abstract class Broker
{
	use SendsAndReceivesCommands;
	
	protected Process $process;
	
	protected DuplexStreamInterface $process_output;
	
	abstract protected function startBackgroundProcess(InputStream $stdin): Process;
	
	public function __construct(
		protected ?LoopInterface $loop = null,
		?InputStream $stdin = null,
	) {
		$this->loop ??= Loop::get();
		
		// Start the server process up
		$stdin ??= new InputStream();
		$this->process = $this->startBackgroundProcess($stdin);
		
		// We're going to pipe outgoing messages to our process's STDIN
		$process_input = new ThroughStream();
		$process_input->on('data', fn($data) => $stdin->write($data));
		
		// And poll for STDOUT to write to our incoming message stream
		$this->process_output = new ThroughStream();
		$this->loop->addPeriodicTimer(0.1, $this->flushIncomingMessageStream(...));
		
		$this->io = new CommandIO($this, $this->process_output, $process_input);
	}
	
	public function debug($message): static
	{
		app(Debugger::class)->debug($message);
		
		return $this;
	}
	
	public function sendCommand(Command $command): static
	{
		$this->io->sendCommand($command);
		
		// Force the process to flush I/O
		$this->process->getStatus();
		
		// Run for at least one cycle
		$this->loop->futureTick(fn() => $this->loop->stop());
		$this->loop->run();
		
		return $this;
	}
	
	protected function flushIncomingMessageStream(): void
	{
		try {
			$this->process_output->write(@$this->process->getIncrementalOutput());
		} catch (Throwable $exception) {
			dd($exception);
		}
	}
	
	protected function artisan(array $arguments, InputStream $stdin): Process
	{
		return new Process(
			command: array_merge([
				(new PhpExecutableFinder())->find(false),
				(new ArtisanExecutableFinder())->find(),
			], $arguments),
			cwd: base_path(),
			env: collect($_ENV)
				->only([
					'APP_ENV',
					'LARAVEL_SAIL',
					'PHP_CLI_SERVER_WORKERS',
					'PHP_IDE_CONFIG',
					'SYSTEMROOT',
					'XDEBUG_CONFIG',
					'XDEBUG_MODE',
					'XDEBUG_SESSION',
				])
				->all(),
			input: $stdin,
		);
	}
}

<?php

namespace Glhd\Dawn;

use BadMethodCallException;
use Closure;
use Glhd\Dawn\Browser\Concerns\ExecutesCommands;
use Glhd\Dawn\Browser\Concerns\HasBrowserAssertionAliases;
use Glhd\Dawn\Browser\Concerns\HasBrowserCommandAliases;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Contracts\BrowserCommand;
use Glhd\Dawn\Contracts\ValueCommand;
use Glhd\Dawn\IO\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use React\EventLoop\LoopInterface;

class Browser
{
	use Macroable;
	use Conditionable;
	use ExecutesCommands;
	use HasBrowserCommandAliases;
	use HasBrowserAssertionAliases;
	
	public readonly string $id;
	
	protected mixed $last_response = null;
	
	public function __construct(
		protected RemoteWebDriverBroker $broker,
		protected LoopInterface $loop,
	) {
		$this->id = (string) Str::uuid();
	}
	
	public function sleep(float $seconds): static
	{
		// Rather than `usleep()`, we'll just run our loop until the timer
		// triggers. This way, other commands can continue to be processed
		// while this sleep operation runs.
		
		$sleeping = true;
		$this->loop->addTimer($seconds, function() use (&$sleeping) {
			$sleeping = false;
			$this->loop->stop();
		});
		
		while ($sleeping) {
			$this->loop->run();
		}
		
		return $this;
	}
	
	public function tap($callback): static
	{
		$callback($this);
		
		return $this;
	}
	
	public function tinker(): static
	{
		if (! class_exists(\Psy\Shell::class)) {
			throw new BadMethodCallException('Psy Shell (required for Tinker) is not installed.');
		}
		
		// Unfortunately, because of the I/O channel, the driver/resolver/page aren't available
		// inside the main process. I'm not sure if there's a solution for that… 
		
		\Psy\Shell::debug([
			'browser' => $this,
			// 'driver' => $this->driver,
			// 'resolver' => $this->resolver,
			// 'page' => $this->page,
		], $this);
		
		return $this;
	}
	
	public function stop(): void
	{
		exit();
	}
	
	public function withLastResponse(Closure $callback): static
	{
		$callback($this->last_response);
		
		return $this;
	}
	
	/**
	 * @return $this|mixed
	 */
	protected function command(Command $command): mixed
	{
		if ($command instanceof BrowserCommand) {
			$command->setBrowser($this);
		}
		
		$this->last_response = $this->broker->sendCommandAndWaitForResponse($command);
		
		return $command instanceof ValueCommand
			? $this->last_response
			: $this;
	}
}

<?php

namespace Glhd\Dawn;

use Closure;
use Glhd\Dawn\Browser\Concerns\HasBrowserAssertionAliases;
use Glhd\Dawn\Contracts\BrowserCommand;
use Glhd\Dawn\Contracts\ValueCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Glhd\Dawn\Browser\Concerns\ExecutesAssertionCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesBrowserCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesCookieCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesDialogCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesElementCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesMouseCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesNavigateCommands;
use Glhd\Dawn\Browser\Concerns\ExecutesWindowCommands;
use Glhd\Dawn\Browser\Concerns\HasBrowserCommandAliases;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\IO\Command;
use React\EventLoop\LoopInterface;

class Browser
{
	use Macroable;
	use Conditionable;
	use ExecutesAssertionCommands;
	use ExecutesBrowserCommands;
	use ExecutesCookieCommands;
	use ExecutesDialogCommands;
	use ExecutesElementCommands;
	use ExecutesMouseCommands;
	use ExecutesNavigateCommands;
	use ExecutesWindowCommands;
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

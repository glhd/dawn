<?php

namespace Glhd\Dawn\Browser;

use Closure;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Glhd\Dawn\Browser;
use Glhd\Dawn\Exceptions\WebDriverNotRunningException;
use Glhd\Dawn\Support\ElementResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * This approach was stolen from the genius `waitForLivewire` method in the 
 * Livewire test suite. It lets us chain on a "wait" operation that requires
 * setup *after* executing code that might have triggered the setup.
 */
class PendingWait
{
	use ForwardsCalls;
	
	public function __construct(
		protected Browser $browser,
		protected Closure $wait,
		protected ?int $seconds = null,
		protected int $interval = 100,
		protected ?string $message = null,
	) {
		
	}
	
	protected function wait()
	{
		return $this->browser->waitUsing($this->seconds, $this->interval, $this->wait);
	}
	
	public function __call(string $name, array $arguments)
	{
		$this->wait();
		
		return $this->forwardCallTo($this->browser, $name, $arguments);
	}
	
	public function __destruct()
	{
		$this->wait();
	}
}

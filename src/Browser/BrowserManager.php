<?php

namespace Glhd\Dawn\Browser;

use Closure;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Glhd\Dawn\Support\ElementResolver;

/**
 * @mixin RemoteWebDriver
 */
class BrowserManager
{
	use ForwardsCalls;
	
	public RemoteWebDriver $driver;
	
	public ElementResolver $resolver;
	
	protected Collection $browsers;
	
	protected Closure $connector;
	
	public function __construct(
		Closure|string $connect,
	) {
		$this->connector = $this->getConnector($connect);
		$this->browsers = new Collection();
	}
	
	public function switchToBrowser(string $browser_id): static
	{
		$managed = $this->getDriver($browser_id);
		
		$this->driver = $managed->driver;
		$this->resolver = $managed->resolver;
		
		return $this;
	}
	
	public function quitAll(): void
	{
		$this->browsers->each(fn(ManagedDriver $driver) => $driver->quit());
	}
	
	public function __call(string $name, array $arguments)
	{
		return $this->forwardDecoratedCallTo($this->driver, $name, $arguments);
	}
	
	protected function getDriver(string $browser_id): ManagedDriver
	{
		if (! $this->browsers->has($browser_id)) {
			$this->browsers->put($browser_id, new ManagedDriver($this->newBrowserConnection()));
		}
		
		return $this->browsers->get($browser_id);
	}
	
	protected function newBrowserConnection(): RemoteWebDriver
	{
		return call_user_func($this->connector, $this);
	}
	
	protected function getConnector(Closure|string $connect): Closure
	{
		if ($connect instanceof Closure) {
			return $connect;
		}
		
		return fn() => RemoteWebDriver::create($connect, DesiredCapabilities::chrome());
	}
}

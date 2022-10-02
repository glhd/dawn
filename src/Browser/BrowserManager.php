<?php

namespace Glhd\Dawn\Browser;

use Closure;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Glhd\Dawn\Exceptions\WebDriverNotRunningException;
use Glhd\Dawn\Support\ElementResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin RemoteWebDriver
 */
class BrowserManager
{
	use ForwardsCalls;
	
	public RemoteWebDriver $driver;
	
	public ElementResolver $resolver;
	
	public ?WebDriverElement $root;
	
	protected Collection $browsers;
	
	protected Closure $connector;
	
	protected ?SeleniumDriverProcess $driver_process = null;
	
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
		$this->driver_process?->signal(SIGKILL);
	}
	
	public function __call(string $name, array $arguments)
	{
		$result = $this->forwardDecoratedCallTo($this->driver, $name, $arguments);
		
		// After each operation, we'll store a reference to the current DOM root so
		// that at any time we can check it for staleness (to look for refreshes, for example)
		$this->root = $this->driver->findElement(WebDriverBy::tagName('html'));
		
		return $result;
	}
	
	protected function getDriver(string $browser_id): ManagedDriver
	{
		if (! $this->browsers->has($browser_id)) {
			$this->browsers->put($browser_id, new ManagedDriver($this->newBrowserConnection()));
		}
		
		return $this->browsers->get($browser_id);
	}
	
	protected function newBrowserConnection(bool $autostart = true): RemoteWebDriver
	{
		try {
			return call_user_func($this->connector, $this);
		} catch (WebDriverCurlException $exception) {
			// If we've already tried to auto-start, then just fail
			if (! $autostart || $this->driver_process) {
				throw new WebDriverNotRunningException($exception);
			}
			
			$this->driver_process = app(SeleniumDriverProcess::class);
			return $this->newBrowserConnection(autostart: false);
		}
	}
	
	protected function getConnector(Closure|string $connect): Closure
	{
		if ($connect instanceof Closure) {
			return $connect;
		}
		
		return function() use ($connect) {
			$capabilities = DesiredCapabilities::chrome();
			
			if (! empty($arguments = $this->defaultChromeArguments())) {
				$capabilities->setCapability(ChromeOptions::CAPABILITY, (new ChromeOptions())->addArguments($arguments));
			}
			
			return RemoteWebDriver::create($connect, $capabilities);
		};
	}
	
	protected function defaultChromeArguments(): array
	{
		$arguments = [];
		
		if (false === config('dawn.browser.sandbox')) {
			$arguments[] = '--no-sandbox';
			$arguments[] = '--disable-dev-shm-usage';
		}
		
		if (config('dawn.browser.headless', true)) {
			$arguments[] = '--headless';
			$arguments[] = '--disable-gpu';
		}
		
		if ($window = config('dawn.browser.window')) {
			$arguments[] = '--window-size='.$window;
		}
		
		return $arguments;
	}
}

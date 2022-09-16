<?php

namespace Glhd\Dawn\Browser;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Traits\ForwardsCalls;
use Glhd\Dawn\Support\ElementResolver;

/**
 * @mixin RemoteWebDriver
 */
class ManagedDriver
{
	use ForwardsCalls;
	
	public ElementResolver $resolver;
	
	public function __construct(
		public RemoteWebDriver $driver,
	) {
		$this->resolver = new ElementResolver($this->driver);
	}
	
	public function __call(string $name, array $arguments)
	{
		return $this->forwardDecoratedCallTo($this->driver, $name, $arguments);
	}
}

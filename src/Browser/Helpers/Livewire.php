<?php

namespace Glhd\Dawn\Browser\Helpers;

use Closure;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Livewire
{
	public function wait(): Closure
	{
		return static function(RemoteWebDriver $driver) {
			return $driver->executeScript('return !! window.Livewire.components.initialRenderIsFinished');
		};
	}
}

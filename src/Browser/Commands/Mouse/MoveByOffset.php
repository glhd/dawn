<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class MoveByOffset extends BrowserCommand
{
	public function __construct(
		public int $x,
		public int $y,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		(new WebDriverActions($manager->driver))
			->moveByOffset($this->x, $this->y)
			->perform();
	}
}

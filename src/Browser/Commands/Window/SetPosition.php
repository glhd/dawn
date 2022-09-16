<?php

namespace Glhd\Dawn\Browser\Commands\Window;

use Facebook\WebDriver\WebDriverPoint;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class SetPosition extends BrowserCommand
{
	public function __construct(
		public int $x,
		public int $y,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()
			->window()
			->setPosition(new WebDriverPoint($this->x, $this->y));
	}
}

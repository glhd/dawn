<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class ReleaseMouse extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		(new WebDriverActions($manager->driver))->release()->perform();
	}
}

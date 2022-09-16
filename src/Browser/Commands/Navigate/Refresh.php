<?php

namespace Glhd\Dawn\Browser\Commands\Navigate;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Refresh extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->navigate()->refresh();
	}
}

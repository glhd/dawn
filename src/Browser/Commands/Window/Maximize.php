<?php

namespace Glhd\Dawn\Browser\Commands\Window;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Maximize extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()->window()->maximize();
	}
}

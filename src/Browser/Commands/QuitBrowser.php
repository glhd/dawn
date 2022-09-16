<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\Browser\BrowserManager;

class QuitBrowser extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->quitAll();
	}
}

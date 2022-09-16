<?php

namespace Glhd\Dawn\Browser\Commands\Cookies;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class DeleteAllCookies extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()->deleteAllCookies();
	}
}

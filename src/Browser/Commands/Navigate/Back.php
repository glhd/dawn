<?php

namespace Glhd\Dawn\Browser\Commands\Navigate;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Back extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->navigate()->back();
	}
}

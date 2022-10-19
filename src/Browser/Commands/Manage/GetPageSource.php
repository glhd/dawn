<?php

namespace Glhd\Dawn\Browser\Commands\Manage;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Contracts\ValueCommand;

class GetPageSource extends BrowserCommand implements ValueCommand
{
	protected function executeWithBrowser(BrowserManager $manager): ?string
	{
		return $manager->getPageSource();
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Dialogs;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class AcceptDialog extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->switchTo()->alert()->accept();
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Dialogs;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class DismissDialog extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->switchTo()->alert()->dismiss();
	}
}

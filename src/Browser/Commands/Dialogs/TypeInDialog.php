<?php

namespace Glhd\Dawn\Browser\Commands\Dialogs;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class TypeInDialog extends BrowserCommand
{
	public function __construct(
		public string $value,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->switchTo()->alert()->sendKeys($this->value);
	}
}

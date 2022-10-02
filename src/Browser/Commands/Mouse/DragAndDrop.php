<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class DragAndDrop extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $from,
		public WebDriverBy|string $to,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		(new WebDriverActions($manager->driver))
			->dragAndDrop(
				source: $manager->resolver->findOrFail($this->from),
				target: $manager->resolver->findOrFail($this->to),
			)
			->perform();
	}
}

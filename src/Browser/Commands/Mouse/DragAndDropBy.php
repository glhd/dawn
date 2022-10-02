<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class DragAndDropBy extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public int $x = 0,
		public int $y = 0,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		(new WebDriverActions($manager->driver))
			->dragAndDropBy(
				source: $manager->resolver->findOrFail($this->selector),
				x_offset: $this->x,
				y_offset: $this->y
			)
			->perform();
	}
}

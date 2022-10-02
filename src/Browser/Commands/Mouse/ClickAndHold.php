<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class ClickAndHold extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string|null $selector
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = (null === $this->selector)
			? $this->selector
			: $manager->resolver->findOrFail($this->selector);
		
		(new WebDriverActions($manager->driver))
			->clickAndHold($element)
			->perform();
	}
}

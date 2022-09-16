<?php

namespace Glhd\Dawn\Browser\Commands\Mouse;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;

class MouseOver extends BrowserCommand
{
	use UsesSelectors;
	
	public WebDriverBy $by;
	
	public function __construct(WebDriverBy|string $by)
	{
		$this->by = $this->selector($by);
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->findElement($this->by);
		
		$manager->getMouse()->mouseMove($element->getCoordinates());
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class ClickButton extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public bool $wait = false
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		if ($this->wait) {
			$html = $manager->findElement(WebDriverBy::tagName('html'));
		}
		
		$manager->resolver->resolveForButtonPress($this->selector)->click();
		
		if ($this->wait) {
			$manager->wait()->until(WebDriverExpectedCondition::stalenessOf($html));
		}
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;

class Click extends BrowserCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string|null $selector,
		public string $resolver = 'find',
		public bool $wait = false,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		if ($this->wait) {
			$html = $manager->findElement(WebDriverBy::tagName('html'));
		}
		
		// If we haven't been provided a selector, then just click wherever the mouse happens to be
		if (null === $this->selector) {
			(new WebDriverActions($manager->driver))->click()->perform();
		} else {
			$manager->resolver->{$this->resolver}($this->selector())->click();
		}
		
		if ($this->wait) {
			$manager->wait()->until(WebDriverExpectedCondition::stalenessOf($html));
		}
	}
}

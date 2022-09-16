<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;

class CheckOrUncheck extends BrowserCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public bool $check = true,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->resolver->resolveForChecking($this->selector);
		
		if (
			$this->needsToBeUnchecked($element)
			|| $this->needsToBeChecked($element)
		) {
			$element->click();
		}
	}
	
	protected function needsToBeChecked(RemoteWebElement $element): bool
	{
		return $element->isSelected() && ! $this->check;
	}
	
	protected function needsToBeUnchecked(RemoteWebElement $element): bool
	{
		return ! $element->isSelected() && $this->check;
	}
}

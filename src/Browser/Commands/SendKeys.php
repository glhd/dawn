<?php

namespace Glhd\Dawn\Browser\Commands;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;

class SendKeys extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public string $keys,
		public bool $clear_input = false,
		public int $pause = 0,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->resolver->resolveForTyping($this->selector);
		
		if ($this->clear_input) {
			$element->clear();
		}
		
		if ($this->pause > 0) {
			$this->sendKeysWithDelay($element);
			return;
		}
		
		$element->sendKeys($this->keys);
	}
	
	protected function sendKeysWithDelay(RemoteWebElement $element)
	{
		foreach (preg_split('//u', $this->keys, -1, PREG_SPLIT_NO_EMPTY) as $key) {
			$element->sendKeys($key);
			usleep($this->pause * 1000);
		}
	}
}

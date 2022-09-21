<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Clear extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->resolver->resolveForTyping($this->selector)->clear();
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Contracts\ValueCommand;

class GetText extends BrowserCommand implements ValueCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager): string
	{
		return $manager->resolver->findOrFail($this->selector)->getText();
	}
}

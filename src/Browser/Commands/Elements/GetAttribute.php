<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Contracts\ValueCommand;

class GetAttribute extends BrowserCommand implements ValueCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public string $attribute,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		return $manager->resolver->findOrFail($this->selector)
			->getAttribute($this->attribute);
	}
}

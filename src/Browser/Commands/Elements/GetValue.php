<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Contracts\ValueCommand;

class GetValue extends BrowserCommand implements ValueCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string $selector
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->resolver->findOrFail($this->selector);
		
		return $element->getAttribute('value');
	}
}

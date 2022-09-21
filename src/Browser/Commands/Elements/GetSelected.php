<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Contracts\ValueCommand;

class GetSelected extends BrowserCommand implements ValueCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public string $value,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager): bool
	{
		$options = $manager->resolver->resolveSelectOptions($this->selector, (array) $this->value);
		
		return collect($options)->contains(fn(RemoteWebElement $option) => $option->isSelected());
	}
}

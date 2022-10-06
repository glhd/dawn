<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertAttributeMissing extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public bool $missing;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public string $attribute,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector);
		
		$this->missing = (null === $element->getAttribute($this->attribute));
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertTrue(
			$this->missing,
			"Saw unexpected attribute [{$this->attribute}] within element [{$selector}]."
		);
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertSeeIn extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $haystack;
	
	public function __construct(
		public string|WebDriverBy $selector,
		public string $needle,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector());
		
		$this->haystack = $element->getText();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertStringContainsString(
			$this->needle,
			$this->haystack,
			"Did not see expected text [{$this->needle}] within element [{$selector}]."
		);
	}
}

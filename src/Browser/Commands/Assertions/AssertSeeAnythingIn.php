<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertSeeAnythingIn extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public string|WebDriverBy $selector
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector());
		
		$this->actual = $element->getText();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertTrue(
			'' !== $this->actual,
			"Saw unexpected text [''] within element [{$selector}]."
		);
	}
}

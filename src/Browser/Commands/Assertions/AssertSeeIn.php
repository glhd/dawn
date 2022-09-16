<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertSeeIn extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public string $selector,
		public string $expected,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->find($this->selector);
		
		$this->actual = $element->getText();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		Assert::assertStringContainsString(
			$this->expected,
			$this->actual,
			"Did not see expected text [{$this->expected}] within element [{$this->selector}]."
		);
	}
}

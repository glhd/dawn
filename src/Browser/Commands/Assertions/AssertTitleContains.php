<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertTitleContains extends BrowserAssertionCommand
{
	public string $actual;
	
	public function __construct(
		public string $expected
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->actual = $manager->getTitle();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		Assert::assertStringContainsString(
			$this->expected,
			$this->actual,
			"Did not see expected text [{$this->expected}] within title [{$this->actual}]."
		);
	}
}

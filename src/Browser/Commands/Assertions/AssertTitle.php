<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertTitle extends BrowserAssertionCommand
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
		Assert::assertEquals(
			$this->expected,
			$this->actual,
			"Expected title [{$this->expected}] does not equal actual title [{$this->actual}]."
		);
	}
}

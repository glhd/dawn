<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertSourceMissing extends BrowserAssertionCommand
{
	public string $haystack;
	
	public function __construct(
		public string $needle,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->haystack = $manager->getPageSource();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		Assert::assertStringNotContainsString(
			$this->needle,
			$this->haystack,
			"Found unexpected source code [{$this->needle}]."
		);
	}
}

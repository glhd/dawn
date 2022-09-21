<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;

class AssertScript extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public string $expression,
		public $expected = true,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$expression = Str::start($this->expression, 'return ');
		
		$this->actual = $manager->executeScript($expression);
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		Assert::assertEquals(
			$this->expected,
			$this->actual,
			"JavaScript expression [{$this->expression}] mismatched."
		);
	}
}

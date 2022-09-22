<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertElementDisplay extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $displayed;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public bool $expected = true,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->displayed = true === $manager->resolver->find($this->selector())?->isDisplayed();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		$state = $this->expected
			? 'is not'
			: 'is';
		
		Assert::assertEquals(
			$this->expected,
			$this->displayed,
			"Element [{$selector}] {$state} visible."
		);
	}
}

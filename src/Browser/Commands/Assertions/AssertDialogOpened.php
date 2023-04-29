<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Exception\NoSuchAlertException;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertDialogOpened extends BrowserAssertionCommand
{
	public ?string $actual;
	
	public function __construct(
		public ?string $expected = null,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		try {
			$this->actual = $manager->switchTo()->alert()->getText();
		} catch (NoSuchAlertException) {
			$this->actual = null;
		}
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		Assert::assertNotNull($this->actual, 'No dialog opened.');
		
		if ($this->expected) {
			Assert::assertEquals(
				$this->expected,
				$this->actual,
				"Expected dialog message [{$this->expected}] does not equal actual message [{$this->actual}]."
			);
		}
	}
}

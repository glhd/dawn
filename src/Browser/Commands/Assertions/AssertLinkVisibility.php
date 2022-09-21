<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Assertions\Concerns\ChecksElementVisibility;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;
use Throwable;

class AssertLinkVisibility extends BrowserAssertionCommand
{
	use ChecksElementVisibility;
	
	public bool $actual;
	
	public function __construct(
		public string $text,
		public bool $expected = true,
		public bool $partial = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		try {
			$selector = $this->partial
				? WebDriverBy::partialLinkText($this->text)
				: WebDriverBy::linkText($this->text);
			
			$manager->findElement($selector);
			
			$this->actual = true; // $this->isElementVisible($manager, $link);
		} catch (Throwable) {
			$this->actual = false;
		}
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$message = $this->expected
			? "Did not see expected link [{$this->text}]."
			: "Saw unexpected link [{$this->text}].";
		
		Assert::assertEquals(
			$this->expected,
			$this->actual,
			$message
		);
	}
}

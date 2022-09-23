<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Assertions\Concerns\DecryptsCookies;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertHasCookie extends BrowserAssertionCommand
{
	use DecryptsCookies;
	
	public ?Cookie $actual;
	
	public function __construct(
		public string $name,
		public ?string $expected = null,
		public bool $decrypt = true,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		try {
			$this->actual = $manager->manage()->getCookieNamed($this->name);
		} catch (NoSuchCookieException) {
			$this->actual = null;
		}
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$value = $this->getValue();
		
		Assert::assertNotNull($value, "Did not find expected cookie [{$this->name}].");
		
		if (null !== $this->expected) {
			Assert::assertEquals(
				$value,
				$this->expected,
				"Cookie [{$this->name}] had value [{$value}], but expected [{$this->expected}]."
			);
		}
	}
}

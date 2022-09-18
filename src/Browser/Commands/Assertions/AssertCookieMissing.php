<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Assert;

class AssertCookieMissing extends BrowserAssertionCommand
{
	public ?Cookie $actual;
	
	public function __construct(
		public string $name,
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
		
		Assert::assertNull($value, "Found unexpected cookie [{$this->name}].");
	}
	
	protected function getValue()
	{
		if (null === $this->actual) {
			return null;
		}
		
		$value = $this->actual->getValue();
		
		if (! $this->decrypt) {
			return $value;
		}
		
		$decrypted = decrypt(rawurldecode($value), unserialize: false);
		
		return $this->shouldRemoveValuePrefix($decrypted)
			? CookieValuePrefix::remove($decrypted)
			: $decrypted;
	}
	
	protected function shouldRemoveValuePrefix($decrypted): bool
	{
		return str_starts_with($decrypted, CookieValuePrefix::create($this->name, Crypt::getKey()));
	}
}

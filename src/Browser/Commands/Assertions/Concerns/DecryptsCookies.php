<?php

namespace Glhd\Dawn\Browser\Commands\Assertions\Concerns;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Assert;

trait DecryptsCookies
{
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

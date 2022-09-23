<?php

namespace Glhd\Dawn\Browser\Commands\Assertions\Concerns;

use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;

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

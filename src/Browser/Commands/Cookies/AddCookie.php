<?php

namespace Glhd\Dawn\Browser\Commands\Cookies;

use DateTimeInterface;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class AddCookie extends BrowserCommand
{
	public function __construct(
		public string $name,
		public ?string $value = null,
		public int|DateTimeInterface|null $expiry = null,
		public array $options = [],
		bool $encrypt = true,
	) {
		if ($encrypt) {
			$prefix = CookieValuePrefix::create($this->name, Crypt::getKey());
			$this->value = encrypt($prefix.$this->value, false);
		}
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()->addCookie(array_merge($this->options, [
			'name' => $this->name,
			'value' => $this->value,
			'expiry' => $this->expiry instanceof DateTimeInterface
				? $this->expiry->getTimestamp()
				: $this->expiry,
		]));
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Cookies;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class DeleteCookie extends BrowserCommand
{
	public function __construct(
		public string $name,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()->deleteCookieNamed($this->name);
	}
}

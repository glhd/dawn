<?php

namespace Glhd\Dawn\Browser\Commands\Wait;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class WaitForUrl extends BrowserCommand
{
	public string $url;
	
	public function __construct(
		string $url,
		protected ?int $timeout = null,
		protected ?int $interval = null,
	) {
		$this->url = url($url);
	}
	
	protected function executeWithBrowser(\Glhd\Dawn\Browser\BrowserManager $manager)
	{
		$manager->wait($this->timeout, $this->interval)
			->until(WebDriverExpectedCondition::urlIs($this->url));
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Manage;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class SwitchTo extends BrowserCommand
{
	public const LOCATORS = [
		'defaultContent',
		'frame',
		'parent',
		'activeElement',
	];
	
	public function __construct(
		public string $locator,
		public WebDriverBy|string|null $selector = null,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		match ($this->locator) {
			'frame' => $manager->switchTo()->frame($manager->resolver->findOrFail($this->selector)),
			default => $manager->switchTo()->{$this->locator}(),
		};
	}
}

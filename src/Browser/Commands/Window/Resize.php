<?php

namespace Glhd\Dawn\Browser\Commands\Window;

use Facebook\WebDriver\WebDriverDimension;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Resize extends BrowserCommand
{
	public function __construct(
		public int $width,
		public int $height,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()
			->window()
			->setSize(new WebDriverDimension($this->width, $this->height));
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Window;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class FitContent extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->switchTo()->defaultContent();
		
		if (! $html = $manager->findElement(WebDriverBy::tagName('html'))) {
			return;
		}
		
		[$width, $height] = $this->getDimensions($html);
		
		if ($width > 0 && $height > 0) {
			$manager->manage()
				->window()
				->setSize(new WebDriverDimension($width, $height));
		}
	}
	
	protected function getDimensions(RemoteWebElement $html): array
	{
		$size = $html->getSize();
		
		return [$size->getWidth(), $size->getHeight()];
	}
}

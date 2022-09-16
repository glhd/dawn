<?php

namespace Glhd\Dawn\Browser\Commands\Window;

use Facebook\WebDriver\WebDriverTargetLocator;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class OpenNewWindow extends BrowserCommand
{
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$existing_handles = $manager->getWindowHandles();
		
		$manager->switchTo()->newWindow(WebDriverTargetLocator::WINDOW_TYPE_WINDOW);
		
		$new_handles = array_diff($manager->getWindowHandles(), $existing_handles);
		
		return reset($new_handles);
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Assertions\Concerns;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Glhd\Dawn\Browser\BrowserManager;

trait ChecksElementVisibility
{
	protected function isElementVisible(BrowserManager $manager, RemoteWebElement $element): bool
	{
		$script = <<<JS
		var elem = arguments[0];
		return !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length );
		JS;
		
		return (bool) $manager->executeScript($script, [$element]);
	}
}

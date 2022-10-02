<?php

namespace Glhd\Dawn\Browser\Helpers;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Glhd\Dawn\Browser\BrowserManager;

class Vue
{
	public function __construct(
		protected BrowserManager $manager,
	) {
	}
	
	public function attribute(RemoteWebElement $element, string $key): mixed
	{
		return $this->manager->executeScript($this->vueAttributeScript($key), [$element]);
	}
	
	protected function vueAttributeScript(string $key): string
	{
		return <<<JS
		var el = arguments[0];

		if (typeof el.__vue__ !== 'undefined') {
			return el.__vue__.{$key};
		}
		
		try {
			var attr = el.__vueParentComponent.ctx.{$key};
			if (typeof attr !== 'undefined') {
				return attr;
			}
		} catch (e) {}
		
		return el.__vueParentComponent.setupState.{$key};
		JS;
	}
}

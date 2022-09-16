<?php

namespace Glhd\Dawn\Browser\Commands\Concerns;

use BadMethodCallException;
use Facebook\WebDriver\WebDriverBy;

trait UsesSelectors
{
	protected function selector(WebDriverBy|string|null $selector = null): WebDriverBy
	{
		if (null === $selector) {
			if (! property_exists($this, 'selector')) {
				throw new BadMethodCallException('Calling selector() without an argument requires a $selector property to be set.');
			}
			
			$selector = $this->selector;
		}
		
		if (is_string($selector)) {
			$selector = WebDriverBy::cssSelector($selector);
		}
		
		return $selector;
	}
}

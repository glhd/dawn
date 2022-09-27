<?php

namespace Glhd\Dawn\Support;

use Facebook\WebDriver\WebDriverBy;

class Selector
{
	public static function from(WebDriverBy|string $selector): WebDriverBy
	{
		return $selector instanceof WebDriverBy
			? $selector
			: WebDriverBy::cssSelector($selector);
	}
	
	public static function toString(WebDriverBy|string $selector): string
	{
		if (is_string($selector)) {
			return $selector;
		}
		
		return match($selector->getMechanism()) {
			'css selector' => $selector->getValue(),
			default => "{$selector->getMechanism()} '{$selector->getValue()}'",
		};
	}
}

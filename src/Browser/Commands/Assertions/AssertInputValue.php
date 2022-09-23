<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertInputValue extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public $value,
		public bool $not = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->actual = $this->getValue($manager);
	}
	
	protected function getValue(BrowserManager $manager): mixed
	{
		$element = $manager->resolver->resolveForTyping($this->selector);
		
		return match ($element->getTagName()) {
			'input', 'textarea' => $element->getAttribute('value'),
			default => $element->getText(),
		};
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		if ($this->not) {
			Assert::assertNotEquals(
				$this->value,
				$this->actual,
				"Value [{$this->value}] for the [{$selector}] input should not equal the actual value."
			);
		}
		
		Assert::assertEquals(
			$this->value,
			$this->actual,
			"Expected value [{$this->value}] for the [{$selector}] input does not equal the actual value [{$this->actual}]."
		);
	}
}

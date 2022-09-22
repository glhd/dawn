<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertAttribute extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public string $attribute,
		public $value,
		public bool $not = false,
		public bool $contains = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector);
		
		$this->actual = $element->getAttribute($this->attribute);
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertNotNull(
			$this->actual,
			"Did not see expected attribute [{$this->attribute}] within element [{$selector}]."
		);
		
		[$assertion, $message] = $this->getAssertion();
		
		$assertion($this->value, $this->actual, sprintf($message, $this->attribute, $this->value, $this->actual));
	}
	
	protected function getAssertion(): array
	{
		return match([$this->not, $this->contains]) {
			[false, false] => [Assert::assertEquals(...), 'Expected \'%s\' attribute [%s] does not equal actual value [%s].'],
			[false, true] => [Assert::assertStringContainsString(...), 'Attribute \'%s\' does not contain [%s]. Full attribute value was [%s].'],
			[true, true] => [Assert::assertStringNotContainsString(...), 'Attribute \'%s\' should not contain [%s].'],
			[true, false] => [Assert::assertNotEquals(...), 'Attribute \'%s\' should not equal [%s].'],
		};
	}
}

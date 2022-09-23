<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertValue extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public bool $is_supported;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public $value,
		public bool $not = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector);
		
		if (! $this->elementSupportsValueAttribute($element)) {
			$this->is_supported = false;
			return;
		}
		
		$this->actual = $element->getAttribute('value');
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		[$assertion, $message] = $this->getAssertion();
		
		$assertion(
			expected: $this->value,
			actual: $this->actual,
			message: sprintf($message, $this->value, $selector)
		);
	}
	
	protected function getAssertion(): array
	{
		if ($this->not) {
			return [Assert::assertNotEquals(...), 'Saw unexpected value [%s] within element [%s].'];
		}
		
		return [Assert::assertEquals(...), 'Did not see expected value [%s] within element [%s].'];
	}
	
	protected function elementSupportsValueAttribute(RemoteWebElement $element): bool
	{
		return in_array($element->getTagName(), [
			'textarea',
			'select',
			'button',
			'input',
			'li',
			'meter',
			'option',
			'param',
			'progress',
		]);
	}
}

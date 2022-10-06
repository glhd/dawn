<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertHasClass extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public array $actual;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public string|array $class,
		public bool $not = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector);
		$class = $element->getAttribute('class') ?? '';
		
		$this->actual = explode(' ', $class);
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		$classes = (array) $this->class;
		
		if ($this->not) {
			$this->performNotContainsAssertions($classes, $selector);
		} else {
			$this->performContainsAssertions($classes, $selector);
		}
	}
	
	protected function performContainsAssertions(array $classes, string $selector): void
	{
		foreach ($classes as $class) {
			Assert::assertContains($class, $this->actual, "Did not find [{$class}] in class list for [{$selector}].");
		}
	}
	
	protected function performNotContainsAssertions(array $classes, string $selector): void
	{
		foreach ($classes as $class) {
			Assert::assertNotContains($class, $this->actual, "Found unexpected [{$class}] in class list for [{$selector}].");
		}
	}
}

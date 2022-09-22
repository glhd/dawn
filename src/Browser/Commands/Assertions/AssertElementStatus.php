<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Support\ElementResolver;
use PHPUnit\Framework\Assert;

class AssertElementStatus extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public bool $exists;
	
	public bool $displayed;
	
	public bool $selected;
	
	public bool $enabled;
	
	public bool $focused;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public bool $expect_exists = true,
		public string $resolver = 'find',
		public ?bool $expect_displayed = null,
		public ?bool $expect_selected = null,
		public ?bool $expect_enabled = null,
		public ?bool $expect_focused = null,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		if (! $element = $this->resolveElement($manager->resolver)) {
			$this->exists = false;
			return;
		}
		
		$this->displayed = $element->isDisplayed();
		$this->selected = $element->isSelected();
		$this->enabled = $element->isEnabled();
		$this->focused = $manager->switchTo()->activeElement()->equals($element);
	}
	
	protected function resolveElement(ElementResolver $resolver): ?RemoteWebElement
	{
		return $resolver->{$this->resolver}($this->selector());
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		$this->performExistenceAssertions($selector);
		$this->performDisplayAssertions($selector);
		$this->performSelectionAssertions($selector);
		$this->performEnabledAssertions($selector);
		$this->performFocusAssertions($selector);
	}
	
	protected function performExistenceAssertions(string $selector)
	{
		if (true === $this->expect_exists) {
			Assert::assertTrue($this->exists, "Element [{$selector}] does not exist.");
		}
		
		if (false === $this->expect_exists) {
			Assert::assertFalse($this->exists, "Element [{$selector}] exists.");
		}
	}
	
	protected function performDisplayAssertions(string $selector)
	{
		if (true === $this->expect_displayed) {
			Assert::assertTrue($this->displayed, "Element [{$selector}] is not displayed.");
		}
		
		if (false === $this->expect_displayed) {
			Assert::assertFalse($this->displayed, "Element [{$selector}] is displayed.");
		}
	}
	
	protected function performSelectionAssertions(string $selector)
	{
		if (true === $this->expect_selected) {
			Assert::assertTrue($this->selected, "Element [{$selector}] is not selected.");
		}
		
		if (false === $this->expect_selected) {
			Assert::assertFalse($this->selected, "Element [{$selector}] is selected.");
		}
	}
	
	protected function performEnabledAssertions(string $selector)
	{
		if (true === $this->expect_enabled) {
			Assert::assertTrue($this->enabled, "Element [{$selector}] is not enabled.");
		}
		
		if (false === $this->expect_enabled) {
			Assert::assertFalse($this->enabled, "Element [{$selector}] is enabled.");
		}
	}
	
	protected function performFocusAssertions(string $selector)
	{
		if (true === $this->expect_focused) {
			Assert::assertTrue($this->focused, "Expected element [{$selector}] to be focused, but it wasn't.");
		}
		
		if (false === $this->expect_focused) {
			Assert::assertFalse($this->focused, "Expected element [{$selector}] not to be focused, but it was.");
		}
	}
}

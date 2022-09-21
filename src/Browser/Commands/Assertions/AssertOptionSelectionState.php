<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertOptionSelectionState extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public bool $selected;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public $value,
		public bool $expected = true,
		public string $message = '',
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$options = $manager->resolver->resolveSelectOptions($this->selector, (array) $this->value);
		
		$this->selected = collect($options)->contains(fn(RemoteWebElement $option) => $option->isSelected());
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertEquals(
			$this->expected,
			$this->selected,
			sprintf($this->message, $this->value, $selector),
		);
	}
}

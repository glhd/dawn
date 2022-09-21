<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertSelectionState extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public bool $selected;
	
	public bool $indeterminate;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public $value,
		public bool $expected = true,
		public bool $expect_indeterminate = false,
		public string $resolver = 'findOrFail',
		public string $message = '',
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->{$this->resolver}($this->selector(), $this->value);
		
		$this->selected = $element->isSelected();
		$this->indeterminate = 'true' === $element->getAttribute('indeterminate');
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		Assert::assertEquals(
			$this->expected,
			$this->selected,
			sprintf($this->message, $selector),
		);
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertOptionPresence extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public int $count;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public array $options,
		public bool $expected = true,
		public string $message = '',
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$options = $manager->resolver->resolveSelectOptions($this->selector, $this->options);
		
		$this->count = collect($options)
			->unique(fn(RemoteWebElement $option) => $option->getAttribute('value'))
			->count();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$selector = $this->selector()->getValue();
		
		$expected = $this->expected
			? count($this->options)
			: 0;
		
		Assert::assertEquals(
			$expected,
			$this->count,
			sprintf($this->message, implode(', ', $this->options), $selector),
		);
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Glhd\Dawn\Browser\Helpers\Vue;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;

class AssertVue extends BrowserAssertionCommand
{
	use UsesSelectors;
	
	public string $actual;
	
	public function __construct(
		public string $key,
		public $value,
		public WebDriverBy|string|null $selector = null,
		public bool $not = false,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$element = $manager->resolver->findOrFail($this->selector ?? '');
		
		$this->actual = (new Vue($manager))->attribute($element, $this->key);
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		[$assertion, $message] = $this->getAssertion();
		
		$assertion($this->value, $this->actual, sprintf($message, json_encode($this->value), $this->key));
	}
	
	protected function getAssertion(): array
	{
		if ($this->not) {
			return [Assert::assertNotEquals(...), 'Saw unexpected value [%s] at the key [%s].'];
		}
		
		return [Assert::assertEquals(...), 'Did not see expected value [%s] at the key [%s].'];
	}
}

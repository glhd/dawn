<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
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
		$script = $this->vueAttributeScript();
		
		$this->actual = $manager->executeScript($script, [$element]);
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
	
	protected function vueAttributeScript(): string
	{
		return <<<JS
		var el = arguments[0];
		if (typeof el.__vue__ !== 'undefined') {
			return el.__vue__.{$this->key};
		}
		try {
			var attr = el.__vueParentComponent.ctx.{$this->key};
			if (typeof attr !== 'undefined') {
				return attr;
			}
		} catch (e) {}
		return el.__vueParentComponent.setupState.{$this->key};
		JS;
	}
}

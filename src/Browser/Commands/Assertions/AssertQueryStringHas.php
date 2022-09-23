<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;
use stdClass;

class AssertQueryStringHas extends BrowserAssertionCommand
{
	public string $actual;
	
	public function __construct(
		public string $name,
		public $value = null,
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->actual = $manager->getCurrentURL();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$parts = parse_url($this->actual);
		
		Assert::assertArrayHasKey(
			'query',
			$parts,
			'Did not see expected query string in ['.$this->actual.'].'
		);
		
		parse_str($parts['query'], $output);
		
		Assert::assertTrue(
			Arr::has($output, $this->name),
			"Did not see expected query string parameter [{$this->name}] in [{$this->actual}]."
		);
		
		if (! $this->value) {
			return;
		}
		
		$actual = Arr::get($output, $this->name, new stdClass());
		
		$actual_for_message = is_array($actual)
			? implode(',', $actual)
			: $actual;
		
		$expected_for_message = is_array($this->value)
			? implode(',', $this->value)
			: $this->value;
		
		Assert::assertEquals(
			$this->value,
			$actual,
			"Query string parameter [{$this->name}] had value [{$actual_for_message}], but expected [{$expected_for_message}]."
		);
	}
}

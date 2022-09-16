<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Illuminate\Support\Arr;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\RegularExpression;

class AssertUrlIs extends BrowserAssertionCommand
{
	public string $actual;
	
	public function __construct(
		public string $expected
	) {
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->actual = $manager->getCurrentURL();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$pattern = str_replace('\*', '.*', preg_quote($this->expected, '/'));
		
		$segments = parse_url($this->actual);
		
		$normalized = sprintf(
			'%s://%s%s%s',
			$segments['scheme'],
			$segments['host'],
			Arr::get($segments, 'port', '')
				? ':'.$segments['port']
				: '',
			Arr::get($segments, 'path', '')
		);
		
		Assert::assertThat(
			$normalized,
			new RegularExpression('/^'.$pattern.'$/u'),
			"Actual URL [{$this->actual}] does not equal expected URL [{$this->expected}]."
		);
	}
}

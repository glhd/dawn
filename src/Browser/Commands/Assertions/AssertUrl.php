<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

class AssertUrl extends BrowserAssertionCommand
{
	public string $actual;
	
	public array $expectations = [];
	
	public static function withExpectation(string $segment, string $operator, ?string $value = null): static
	{
		return (new static())->expect($segment, $operator, $value);
	}
	
	public function __construct(?string $expected = null)
	{
		if (null !== $expected) {
			$this->expect('url', '=', $expected);
		}
	}
	
	public function expect(string $segment, string $operator, ?string $value = null): static
	{
		if (! in_array($operator, ['=', '!=', 'starts_with', 'has', 'missing'])) {
			throw new InvalidArgumentException("Invalid operator: '$operator'");
		}
		
		$this->expectations[] = [$segment, $operator, $value];
		
		return $this;
	}
	
	protected function loadData(BrowserManager $manager): void
	{
		$this->actual = $manager->getCurrentURL();
	}
	
	protected function performAssertions(RemoteWebDriverBroker $broker): void
	{
		$segments = parse_url($this->actual);
		
		$segments['url'] = sprintf(
			'%s://%s%s%s',
			$segments['scheme'],
			$segments['host'],
			Arr::get($segments, 'port', '')
				? ':'.$segments['port']
				: '',
			Arr::get($segments, 'path', '')
		);
		
		foreach ($this->expectations as $expectation) {
			[$key, $operator, $expected] = $expectation;
			$actual = $segments[$key] ?? '';
			
			$pattern = '/^'.str_replace('\*', '.*', preg_quote($expected, '/')).'$/u';
			
			if ('=' === $operator) {
				Assert::assertMatchesRegularExpression(
					$pattern,
					$actual,
					"Actual {$key} [$actual] does not equal expected {$key} [$expected]."
				);
			}
			
			if ('!=' === $operator) {
				Assert::assertDoesNotMatchRegularExpression(
					$pattern,
					$actual,
					ucfirst($key)." [$actual] should not equal the actual value."
				);
			}
			
			if ('starts_with' === $operator) {
				Assert::assertStringStartsWith(
					$expected,
					$actual,
					"Actual {$key} [$actual] does not begin with expected {$key} [$expected]."
				);
			}
		}
	}
}

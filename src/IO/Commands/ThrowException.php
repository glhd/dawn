<?php

namespace Glhd\Dawn\IO\Commands;

use Exception;
use Glhd\Dawn\IO\Command;
use Glhd\Dawn\Support\Broker;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ThrowException extends Command
{
	public static function runtime(string $message): self
	{
		return new self(new RuntimeException($message));
	}
	
	public static function invalidArgument(string $name, string $expected, mixed $actual): self
	{
		$got = get_debug_type($actual);
		
		return new self(new InvalidArgumentException("{$name} expected argument of type '{$expected}' but got '{$got}'."));
	}
	
	public function __construct(
		public Throwable|string $exception,
	) {
	}
	
	public function execute(Broker $_)
	{
		if (is_string($this->exception)) {
			$this->exception = new Exception($this->exception);
		}
		
		throw $this->exception;
	}
	
	public function toData(): string
	{
		try {
			return parent::toData();
		} catch (Throwable $exception) {
			// If the full exception can't be serialized, then we'll just send the message
			if (! is_string($this->exception)) {
				$this->exception = $this->exception->getMessage();
				return parent::toData();
			}
			
			// If we already have a string, we'll just re-throw
			throw $exception;
		}
	}
}

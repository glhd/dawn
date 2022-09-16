<?php

namespace Glhd\Dawn\IO;

use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Stringable;
use Throwable;

abstract class Command implements Stringable
{
	public ?string $id = null;
	
	public static function fromData(string $data): self
	{
		try {
			$result = unserialize(base64_decode(trim($data)));
			
			if ($result instanceof self) {
				return $result;
			}
			
			$got = get_debug_type($result);
			throw new InvalidArgumentException("Expected Dawn message object, but got '{$got}' instead.");
		} catch (Throwable $exception) {
			throw new RuntimeException("Unable to process message: '{$data}'", $exception->getCode(), $exception);
		}
	}
	
	public function toData(): string
	{
		$this->id ??= (string) Str::uuid();
		
		return base64_encode(serialize($this))."\n";
	}
	
	public function __toString(): string
	{
		return $this->toData();
	}
}

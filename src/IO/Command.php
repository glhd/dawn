<?php

namespace Glhd\Dawn\IO;

use Glhd\Dawn\Exceptions\UnableToInstantiateCommandFromData;
use Glhd\Dawn\Exceptions\UnexpectedCommandType;
use Illuminate\Support\Str;
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
			
			throw new UnexpectedCommandType(static::class, $result);
		} catch (Throwable $exception) {
			throw new UnableToInstantiateCommandFromData($data, $exception);
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

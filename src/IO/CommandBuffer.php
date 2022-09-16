<?php

namespace Glhd\Dawn\IO;

use BadMethodCallException;
use Closure;
use Illuminate\Support\Str;

class CommandBuffer
{
	protected bool $open = true;
	
	protected string $buffer = '';
	
	public function __construct(
		protected Closure $callback
	) {
	}
	
	public function write(string $chunk): static
	{
		if (! $this->open) {
			throw new BadMethodCallException('Cannot write to a closed message buffer.');
		}
		
		$this->buffer .= $chunk;
		
		$this->emit();
		
		return $this;
	}
	
	public function close(): static
	{
		$this->emit();
		
		$this->open = false;
		
		return $this;
	}
	
	protected function emit(): void
	{
		if (! str_contains($this->buffer, "\n")) {
			return;
		}
		
		$chunks = Str::of($this->buffer)->explode("\n");
		
		// Since the last chunk is either a new line or a partial line,
		// we'll push it back to the buffer until we get more data
		$this->buffer = $chunks->pop();
		
		$chunks->filter()
			->map(fn(string $chunk) => Command::fromData($chunk))
			->each($this->callback);
	}
}

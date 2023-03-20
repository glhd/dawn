<?php

namespace Glhd\Dawn\IO;

use BadMethodCallException;
use Closure;
use Glhd\Dawn\Exceptions\UnableToInstantiateCommandFromData;
use Illuminate\Support\Str;
use RuntimeException;

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
		
		try {
			$chunks->filter()
				->map(fn(string $chunk) => Command::fromData($chunk))
				->each($this->callback);
		} catch (UnableToInstantiateCommandFromData) {
			// If we're unable to process a command, we'll assume that there's error output
			// that could not be serialized as a command. If that's the case, it's best to just
			// dump the whole message for debugging purposes (better of two evils).
			throw new RuntimeException("Error processing background output:\n\n".$chunks->join("\n"));
		}
	}
}

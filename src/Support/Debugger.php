<?php

namespace Glhd\Dawn\Support;

use Closure;

class Debugger
{
	public function __construct(
		protected ?Closure $callback = null
	) {
	}
	
	public function debug($message): void
	{
		if ($this->callback) {
			call_user_func($this->callback, $message);
		}
	}
}

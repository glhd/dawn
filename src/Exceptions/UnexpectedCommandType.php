<?php

namespace Glhd\Dawn\Exceptions;

use InvalidArgumentException;

class UnexpectedCommandType extends InvalidArgumentException
{
	public function __construct(
		public string $command,
		public $result
	) {
		parent::__construct(sprintf(
			"Expected Dawn '%s' object, but got '%s' instead.",
			class_basename($this->command),
			get_debug_type($result)
		));
	}
}

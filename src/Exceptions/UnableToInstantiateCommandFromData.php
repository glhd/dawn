<?php

namespace Glhd\Dawn\Exceptions;

use RuntimeException;
use Throwable;

class UnableToInstantiateCommandFromData extends RuntimeException
{
	public function __construct(
		public string $data,
		Throwable $previous
	) {
		parent::__construct("Unable to process message: '{$data}'", previous: $previous);
	}
}

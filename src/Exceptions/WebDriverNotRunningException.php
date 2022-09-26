<?php

namespace Glhd\Dawn\Exceptions;

use RuntimeException;
use Throwable;

class WebDriverNotRunningException extends RuntimeException
{
	public function __construct(Throwable $previous)
	{
		$message = 'A web driver instance is not running and Dawn was not '
			.'able to automatically start one. Make sure you have chromedriver installed.';
		
		parent::__construct($message, previous: $previous);
	}
}

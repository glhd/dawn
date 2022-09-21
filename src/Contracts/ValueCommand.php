<?php

namespace Glhd\Dawn\Contracts;

use Glhd\Dawn\IO\Command;
use Glhd\Dawn\IO\CommandIO;
use Glhd\Dawn\IO\Commands\Notice;
use Glhd\Dawn\IO\Commands\ThrowException;
use Throwable;

interface ValueCommand
{
	// Indicates that the purpose of this command is to return a value
	// (rather than execute some remote function).
}

<?php

namespace Glhd\Dawn\Concerns;

use Glhd\Dawn\IO\Command;
use Glhd\Dawn\IO\CommandIO;
use Glhd\Dawn\IO\Commands\Notice;
use Glhd\Dawn\IO\Commands\ThrowException;
use Throwable;

trait SendsAndReceivesCommands
{
	protected CommandIO $io;
	
	public function sendCommand(Command $command): static
	{
		$this->io->sendCommand($command);
		
		return $this;
	}
	
	public function sendNotice(string $message): static
	{
		return $this->sendCommand(new Notice($message));
	}
	
	public function sendException(string|Throwable $exception): static
	{
		return $this->sendCommand(new ThrowException($exception));
	}
}

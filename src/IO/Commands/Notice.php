<?php

namespace Glhd\Dawn\IO\Commands;

use Glhd\Dawn\IO\Command;
use Glhd\Dawn\Support\Broker;
use Glhd\Dawn\Support\Debugger;

class Notice extends Command
{
	public function __construct(
		public string $message
	) {
	}
	
	public function execute(Broker $broker)
	{
		app(Debugger::class)->debug($this->message);
	}
}

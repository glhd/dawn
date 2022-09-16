<?php

namespace Glhd\Dawn\Console\Commands;

use Illuminate\Console\Command;
use Glhd\Dawn\Browser\RemoteWebDriverProcess;
use Glhd\Dawn\IO\Commands\ThrowException;
use Throwable;

class DriveCommand extends Command
{
	protected $signature = 'dawn:drive {url?}';
	
	protected $hidden = true;
	
	public function handle()
	{
		try {
			new RemoteWebDriverProcess($this->argument('url'));
		} catch (Throwable $exception) {
			$this->line(new ThrowException($exception));
		}
	}
}

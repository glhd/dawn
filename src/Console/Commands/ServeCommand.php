<?php

namespace Glhd\Dawn\Console\Commands;

use Glhd\Dawn\Http\WebServerProcess;
use Glhd\Dawn\IO\Commands\ThrowException;
use Illuminate\Console\Command;
use Throwable;

class ServeCommand extends Command
{
	protected $signature = 'dawn:serve {host=127.0.0.1} {port=8089} {public_path?}';
	
	protected $hidden = true;
	
	public function handle()
	{
		try {
			new WebServerProcess(
				public_path: $this->argument('public_path') ?? public_path(),
				host: $this->argument('host'),
				port: (int) $this->argument('port'),
			);
		} catch (Throwable $exception) {
			$this->line(new ThrowException($exception));
		}
	}
}

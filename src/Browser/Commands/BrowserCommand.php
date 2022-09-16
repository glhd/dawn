<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\InteractsWithBrowserInstance;
use Glhd\Dawn\Browser\RemoteWebDriverProcess;
use Glhd\Dawn\IO\Command;

abstract class BrowserCommand extends Command
{
	use InteractsWithBrowserInstance;
	
	abstract protected function executeWithBrowser(BrowserManager $manager);
	
	public function execute(RemoteWebDriverProcess $server)
	{
		$server->browser_manager->switchToBrowser($this->browser_id);
		
		$server->respond($this, $this->executeWithBrowser($server->browser_manager));
	}
}

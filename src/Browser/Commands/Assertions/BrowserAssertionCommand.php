<?php

namespace Glhd\Dawn\Browser\Commands\Assertions;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\InteractsWithBrowserInstance;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Browser\RemoteWebDriverProcess;
use Glhd\Dawn\Contracts\BrowserCommand;
use Glhd\Dawn\IO\Command;
use Throwable;

abstract class BrowserAssertionCommand extends Command implements BrowserCommand
{
	use InteractsWithBrowserInstance;
	
	abstract protected function loadData(BrowserManager $manager): void;
	
	abstract protected function performAssertions(RemoteWebDriverBroker $broker): void;
	
	public function execute(RemoteWebDriverProcess|RemoteWebDriverBroker $context)
	{
		// If we're executing on the web driver, we'll load all the data necessary for running the assertions
		if ($context instanceof RemoteWebDriverProcess) {
			try {
				$context->browser_manager->switchToBrowser($this->browser_id);
				$this->loadData($context->browser_manager);
				$context->sendCommand($this);
				$context->respond($this);
			} catch (Throwable $exception) {
				$context->sendException($exception);
			}
		}
		
		// If we're executing in the main process, we'll run the assertions
		if ($context instanceof RemoteWebDriverBroker) {
			$this->performAssertions($context);
		}
	}
}

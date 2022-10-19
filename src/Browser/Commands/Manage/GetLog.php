<?php

namespace Glhd\Dawn\Browser\Commands\Manage;

use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class GetLog extends BrowserCommand
{
	public static $supported = [
		WebDriverBrowserType::CHROME,
		WebDriverBrowserType::PHANTOMJS,
	];
	
	public function __construct(
		public string $save_as,
		public string $log_type = 'browser',
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		if (! in_array($manager->getCapabilities()->getBrowserName(), static::$supported)) {
			return;
		}
		
		if (empty($logs = $manager->manage()->getLog($this->log_type))) {
			return;
		}
		
		file_put_contents($this->save_as, json_encode($logs, JSON_PRETTY_PRINT));
	}
}

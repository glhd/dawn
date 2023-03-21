<?php

namespace Glhd\Dawn\Browser\Commands\Manage;

use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\NormalizesStoragePaths;

class GetLog extends BrowserCommand
{
	use NormalizesStoragePaths;
	
	public static array $supported = [
		WebDriverBrowserType::CHROME,
		WebDriverBrowserType::PHANTOMJS,
	];
	
	public string $filename;
	
	public function __construct(
		string $filename,
		public string $log_type = 'browser',
	) {
		$this->filename = $this->prepareAndNormalizeStoragePath(
			filename: $filename,
			directory: config('dawn.storage_logs', resource_path('dawn/logs')),
		);
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		if (! in_array($manager->getCapabilities()->getBrowserName(), static::$supported)) {
			return;
		}
		
		if (empty($logs = $manager->manage()->getLog($this->log_type))) {
			return;
		}
		
		file_put_contents($this->filename, json_encode($logs, JSON_PRETTY_PRINT));
	}
}

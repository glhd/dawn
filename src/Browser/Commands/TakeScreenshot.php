<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\Concerns\NormalizesStoragePaths;

class TakeScreenshot extends BrowserCommand
{
	use NormalizesStoragePaths;
	
	public string $filename;
	
	public function __construct(string $filename)
	{
		$this->filename = $this->prepareAndNormalizeStoragePath(
			filename: $filename,
			directory: config('dawn.storage_screenshots', resource_path('dawn/screenshots')),
		);
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->takeScreenshot($this->filename);
	}
}

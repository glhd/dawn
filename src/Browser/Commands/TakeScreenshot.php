<?php

namespace Glhd\Dawn\Browser\Commands;

class TakeScreenshot extends BrowserCommand
{
	public function __construct(
		public string $filename
	) {
	}
	
	protected function executeWithBrowser(\Glhd\Dawn\Browser\BrowserManager $manager)
	{
		$manager->takeScreenshot($this->filename);
	}
}

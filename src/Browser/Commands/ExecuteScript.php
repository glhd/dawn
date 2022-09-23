<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\Browser\BrowserManager;
use Illuminate\Support\Collection;

class ExecuteScript extends BrowserCommand
{
	public Collection $scripts;
	
	public function __construct(string|array $scripts)
	{
		$this->scripts = collect((array) $scripts);
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$this->scripts->each(fn($script) => $manager->executeScript($script));
	}
}

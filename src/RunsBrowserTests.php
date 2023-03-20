<?php

namespace Glhd\Dawn;

use Glhd\Dawn\Support\ProcessManager;
use Illuminate\Support\Collection;

trait RunsBrowserTests
{
	protected ?Collection $browsers = null;
	
	protected function openBrowser(): Browser
	{
		$this->browsers->push($browser = $this->app->make(Browser::class));
		
		return $browser;
	}
	
	protected function setUpRunsBrowserTests(): void
	{
		$this->browsers = new Collection();
	}
	
	protected function tearDownRunsBrowserTests(): void
	{
		$this->browsers->each(fn(Browser $browser) => $browser->quit());
	}
}

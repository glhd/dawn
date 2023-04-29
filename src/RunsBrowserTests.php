<?php

namespace Glhd\Dawn;

use Glhd\Dawn\Http\WebServerBroker;
use Illuminate\Routing\UrlGenerator;
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
		
		// We want URLs in Dawn tests to go thru the Dawn proxy
		app(UrlGenerator::class)
			->forceRootUrl(app(WebServerBroker::class)->url());
	}
	
	protected function tearDownRunsBrowserTests(): void
	{
		$this->browsers->each(fn(Browser $browser) => $browser->quit());
	}
}

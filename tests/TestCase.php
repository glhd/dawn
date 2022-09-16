<?php

namespace Glhd\Dawn\Tests;

use Glhd\Dawn\Providers\DawnServiceProvider;
use Glhd\Dawn\RunsBrowserTests;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
	use RunsBrowserTests;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->setUpRunsBrowserTests();
		
		View::getFinder()->addLocation(__DIR__.'/views');
		
		// This forces the testbench binary to load our service provider on the CLI
		file_put_contents(base_path('testbench.yaml'), "providers:\n  - ".DawnServiceProvider::class);
	}
	
	protected function tearDown(): void
	{
		$this->tearDownRunsBrowserTests();
		
		// Clean up our custom yaml file
		if (file_exists(base_path('testbench.yaml'))) {
			unlink(base_path('testbench.yaml'));
		}
		
		parent::tearDown();
	}
	
	protected function getPackageProviders($app)
	{
		return [
			DawnServiceProvider::class,
		];
	}
	
	protected function getPackageAliases($app)
	{
		return [];
	}
	
	protected function getApplicationTimezone($app)
	{
		return 'America/New_York';
	}
}

<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class WaitTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_location_waits(): void
	{
		Route::get('a', function() {
			return response('<a href="/b">Go to B</a>', headers: ['Content-Type' => 'text/html']);
		});
		
		Route::get('b', function() {
			return response('<a href="/c">Go to C</a>', headers: ['Content-Type' => 'text/html']);
		});
		
		Route::get('c', function() {
			return response('<a href="/a">Go to A</a>', headers: ['Content-Type' => 'text/html']);
		});
		
		$this->expectNotToPerformAssertions();
		
		$this->openBrowser()
			->visit('/a')
			->clickLink('Go to B')
			->waitForLocation('/b')
			->clickLink('Go to C')
			->waitForLocation('/c')
			->clickLink('Go to A')
			->waitForLocation('/a');
	}
}

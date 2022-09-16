<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class BrowserTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_basic_browser_interactions(): void
	{
		$this->withoutExceptionHandling();
		
		Route::view('/hello-world', 'hello-world');
		
		$this->openBrowser()
			->resize(1280, 940)
			->visit('/hello-world')
			->assertSeeIn('@content', 'Hello');
	}
}

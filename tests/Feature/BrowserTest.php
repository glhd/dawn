<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class BrowserTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_basic_alpine_interactions(): void
	{
		Route::view('/', 'basic-alpine');
		
		$browser = $this->openBrowser();
		
		$browser
			->visit('/')
			->script('window.greeting = "Hello";')
			->type('.name', 'Chris')
			->press('.hello')
			->assertDialogOpened('Hello Chris!')
			->acceptDialog();
		
		$this->assertEquals('Chris', $browser->value('.name'));
		
		$browser->value('.name', 'Tim')
			->press('.hello')
			->assertDialogOpened('Hello Tim!');
	}
}

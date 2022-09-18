<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BrowserTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_basic_browser_interactions(): void
	{
		$expected = Str::random();
		
		Route::get('/', function() use ($expected) {
			return response(view('hello-world'))
				->cookie(cookie('Foo', $expected, 10));
		})->middleware(EncryptCookies::class);
		
		// $this->actingAs($user);
		
		$this->openBrowser()
			->resize(1280, 720)
			->visit('/')
			->assertSeeIn('@content', 'Hello')
			->assertHasCookie('Foo', $expected)
			->sleep(1);
	}
	
	public function test_basic_alpine_interactions(): void
	{
		Route::view('/', 'basic-alpine');
		
		$this->openBrowser()
			->resize(1280, 720)
			->visit('/')
			->script('window.greeting = "Hello";')
			->type('.name', 'Chris')
			->press('.hello')
			->assertDialogOpened('Hello Chris!');
	}
}

<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ElementResolutionTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_see_assertions(): void
	{
		Route::view('/', 'resolvers.buttons');
		
		$this->openBrowser()
			->visit('/')
			->assertSeeIn('#clicked', 'N/A')
			->clickButton('#button-with-id')
			->assertSeeIn('#clicked', 'button with id')
			->clickButton('.button-with-class')
			->assertSeeIn('#clicked', 'button with class')
			->clickButton('submit-input')
			->assertSeeIn('#clicked', 'submit input')
			->clickButton('submit-input')
			->assertSeeIn('#clicked', 'submit input')
			->clickButton('button-input')
			->assertSeeIn('#clicked', 'button input')
			->clickButton('button-with-name')
			->assertSeeIn('#clicked', 'button with name')
			->clickButton('submit button by value 1')
			->assertSeeIn('#clicked', 'submit button by value 1')
			->clickButton('submit button by value 2')
			->assertSeeIn('#clicked', 'submit button by value 2')
			->clickButton('Button With Text')
			->assertSeeIn('#clicked', 'button with text');
	}
}

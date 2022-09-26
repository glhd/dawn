<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class SeeAssertionsTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_see_assertions(): void
	{
		Route::view('/', 'see');
		
		$this->openBrowser()
			->visit('/')
			->assertSeeLink('Visible Link')
			->assertDontSeeLink('Hidden Link');
	}
}

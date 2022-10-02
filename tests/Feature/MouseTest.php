<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class MouseTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_mouse_movement(): void
	{
		Route::view('/', 'mouse');
		
		// FIXME: Drag and drop tests
		
		$this->openBrowser()
			->visit('/')
			->assertSeeIn('#status', 'N/A')
			->clickAndHold('#click-and-hold')
			->assertSeeIn('#status', 'Mouse Down')
			->releaseMouse()
			->assertSeeIn('#status', 'Mouse Up')
			->doubleClick('#double-click')
			->assertSeeIn('#status', '2')
			->rightClick('#right-click')
			->assertSeeIn('#status', 'Right Click');
	}
}

<?php

namespace Glhd\Dawn\Tests\Feature;

use Glhd\Dawn\RunsBrowserTests;
use Glhd\Dawn\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

class UploadTest extends TestCase
{
	use RunsBrowserTests;
	
	public function test_wait_for_reload(): void
	{
		$file = null;
		
		Route::view('/', 'upload');
		Route::post('/', function(Request $request) use (&$file) {
			$file = $request->file('upload');
		});
		
		$browser = $this->openBrowser()->visit('/');
		
		// Photo by [Chris Henry](https://unsplash.com/@chrishenryphoto) on [Unsplash](https://unsplash.com/photos/E77SjOPCE5Y)
		// We're using a decently-sized image to test larger file handling
		$browser->attach('#upload-input', __DIR__.'/../resources/test-upload.jpg');
		$browser->clickAndWaitForReload('button');
		
		$this->assertInstanceOf(UploadedFile::class, $file);
		$this->assertEquals('test-upload.jpg', $file->getClientOriginalName());
		$this->assertEquals('image/jpeg', $file->getClientMimeType());
		$this->assertEquals(filesize(__DIR__.'/../resources/test-upload.jpg'), $file->getSize());
	}
}

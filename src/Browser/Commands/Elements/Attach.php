<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class Attach extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public string $path,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->resolver->resolveForAttachment($this->selector);
		
		$element->setFileDetector((new LocalFileDetector()))->sendKeys($this->path);
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Wait;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;

class WaitForEvent extends BrowserCommand
{
	public function __construct(
		public string $event,
		public WebDriverBy|string|null $selector,
		public ?int $seconds,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->manage()->timeouts()->setScriptTimeout($this->seconds ?? 5);
		
		$target = in_array($this->selector, ['document', 'window'])
			? $this->selector
			: $manager->resolver->findOrFail($this->selector);
		
		$manager->executeAsyncScript($this->waitScript(), [
			$target,
			$this->event,
		]);
	}
	
	protected function waitScript(): string
	{
		// Using `eval()` here accounts for cases where we're listinging
		// on the document or window objects. The third argument is provided
		// by `executeAsyncScript` (the callback).
		
		return <<<JS
		var el = eval(arguments[0]);
		var event = arguments[1];
		var callback = arguments[2];
		el.addEventListener(event, () => callback(), { once: true });
		JS;
	}
}

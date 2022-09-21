<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Illuminate\Support\Js;

class SetValue extends BrowserCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public $value,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$selector = json_encode($manager->resolver->format($this->selector()));
		$value = Js::from($this->value);
		
		$script = <<<JS
		(function() {
			let input = document.querySelector({$selector});
			input.value = {$value};
			input.dispatchEvent(new Event('input', { bubbles: true }));
		})();
		JS;
		
		$manager->executeScript($script);
		
		return null;
	}
}

<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Illuminate\Support\Str;

class SendKeys extends BrowserCommand
{
	public function __construct(
		public WebDriverBy|string $selector,
		public string|array $keys,
		public bool $clear_input = false,
		public int $pause = 0,
	) {
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		if (is_array($this->keys)) {
			$this->sendKeysDirectly($manager);
			return;
		}
		
		$element = $manager->resolver->resolveForTyping($this->selector);
		
		if ($this->clear_input) {
			$element->clear();
		}
		
		if ($this->pause > 0) {
			$this->sendKeysWithDelay($element);
			return;
		}
		
		$element->sendKeys($this->keys);
	}
	
	protected function sendKeysDirectly(BrowserManager $manager)
	{
		$manager->resolver->findOrFail($this->selector)
			->sendKeys($this->parseKeys($this->keys));
	}
	
	protected function sendKeysWithDelay(RemoteWebElement $element)
	{
		foreach (preg_split('//u', $this->keys, -1, PREG_SPLIT_NO_EMPTY) as $key) {
			$element->sendKeys($key);
			usleep($this->pause * 1000);
		}
	}
	
	protected function parseKeys(array $keys): array
	{
		return collect($keys)
			->map(function($key) {
				if (is_string($key) && Str::startsWith($key, '{') && Str::endsWith($key, '}')) {
					$key = constant(WebDriverKeys::class.'::'.strtoupper(trim($key, '{}')));
				}
				
				if (is_array($key) && Str::startsWith($key[0], '{')) {
					$key[0] = constant(WebDriverKeys::class.'::'.strtoupper(trim($key[0], '{}')));
				}
				
				return $key;
			})
			->all();
	}
}

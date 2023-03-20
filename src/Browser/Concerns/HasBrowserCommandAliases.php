<?php

namespace Glhd\Dawn\Browser\Concerns;

use Closure;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Helpers\Livewire;
use Glhd\Dawn\Browser\Helpers\Vue;
use Glhd\Dawn\Support\Selector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use InvalidArgumentException;
use stdClass;

trait HasBrowserCommandAliases
{
	public function clickLink(string $text, bool $wait = false): static
	{
		return $this->click(WebDriverBy::linkText($text), wait: $wait);
	}
	
	public function clickButton(string|WebDriverBy $selector, bool $wait = false): static
	{
		return $this->click($selector, resolver: 'resolveForButtonPress', wait: $wait);
	}
	
	public function radio(string|WebDriverBy $selector): static
	{
		return $this->clickRadio($selector);
	}
	
	public function check(string|WebDriverBy $selector): static
	{
		return $this->checkOrUncheck($selector, true);
	}
	
	public function uncheck(string|WebDriverBy $selector): static
	{
		return $this->checkOrUncheck($selector, false);
	}
	
	public function move(int $x = 0, int $y = 0): static
	{
		return $this->setPosition($x, $y);
	}
	
	public function visitRoute($name, $parameters = [], $absolute = true): static
	{
		return $this->visit(route($name, $parameters, $absolute));
	}
	
	public function script(string|array $scripts): static
	{
		return $this->executeScript($scripts);
	}
	
	public function keys(string|WebDriverBy $selector, ...$keys): static
	{
		return $this->sendKeys($selector, $keys);
	}
	
	public function type(WebDriverBy|string $selector, string $keys): static
	{
		return $this->sendKeys($selector, $keys, true, 0);
	}
	
	public function typeSlowly(WebDriverBy|string $selector, string $keys, int $pause = 100): static
	{
		return $this->sendKeys($selector, $keys, true, $pause);
	}
	
	public function append(WebDriverBy|string $selector, string $keys): static
	{
		return $this->sendKeys($selector, $keys, false, 0);
	}
	
	public function appendSlowly(WebDriverBy|string $selector, string $keys, int $pause = 100): static
	{
		return $this->sendKeys($selector, $keys, false, $pause);
	}
	
	/** @return $this|mixed */
	public function value(WebDriverBy|string $selector, $value = null): mixed
	{
		return null === $value
			? $this->getValue($selector)
			: $this->setValue($selector, $value);
	}
	
	public function quit(): static
	{
		return $this->quitBrowser();
	}
	
	public function press(WebDriverBy|string $selector, bool $wait = false): static
	{
		return $this->clickButton($selector, $wait);
	}
	
	public function selected(WebDriverBy|string $selector, string $value): bool
	{
		return $this->getSelected($selector, $value);
	}
	
	public function waitFor(WebDriverBy|string $selector, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: WebDriverExpectedCondition::presenceOfElementLocated(Selector::from($selector)),
			message: 'Did not find selector before timeout.',
		);
	}
	
	public function waitUntilMissing(WebDriverBy|string $selector, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: WebDriverExpectedCondition::not(
				WebDriverExpectedCondition::presenceOfElementLocated(Selector::from($selector))
			),
			message: 'Selector was not removed before timeout.',
		);
	}
	
	public function waitForTextIn(WebDriverBy|string $selector, string $text, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: WebDriverExpectedCondition::elementTextContains(
				by: Selector::from($selector),
				text: $text,
			),
			message: "Did not see text [{$text}] in selector [".Selector::toString($selector).'] before timeout.',
		);
	}
	
	public function waitForText(string $text, ?int $seconds = null): static
	{
		return $this->waitForTextIn('body', $text, $seconds);
	}
	
	public function waitUntilMissingTextIn(WebDriverBy|string $selector, string $text, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: WebDriverExpectedCondition::not(
				WebDriverExpectedCondition::elementTextContains(
					by: Selector::from($selector),
					text: $text,
				)
			),
			message: "Text [{$text}] in selector [".Selector::toString($selector).'] was not removed before timeout.',
		);
	}
	
	public function waitUntilMissingText(string $text, ?int $seconds = null): static
	{
		return $this->waitUntilMissingTextIn('body', $seconds);
	}
	
	public function waitForLink(string $link, ?int $seconds = null): static
	{
		return $this->waitFor(WebDriverBy::linkText($link), $seconds);
	}
	
	public function waitForInput(string $field, ?int $seconds = null): static
	{
		return $this->waitFor("input[name='{$field}'], textarea[name='{$field}'], select[name='{$field}']", $seconds);
	}
	
	public function waitForLocation(string $path, ?int $seconds = null): static
	{
		$message = "Waited for location [{$path}] but timed out.";
		$path = Js::from($path);
		
		return Str::startsWith($path, ['http://', 'https://'])
			? $this->waitUntil('`${location.protocol}//${location.host}${location.pathname}` == '.$path, $seconds, $message)
			: $this->waitUntil("window.location.pathname == {$path}", $seconds, $message);
	}
	
	public function waitForRoute(string $route, $parameters = [], ?int $seconds = null): static
	{
		return $this->waitForLocation(route($route, $parameters, false), $seconds);
	}
	
	public function waitUntil(string $script, ?int $seconds = null, string $message = null): static
	{
		$script = (string) Str::of($script)
			->start('return ')
			->finish(';');
		
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(RemoteWebDriver $driver) use ($script) {
				return $driver->executeScript($script);
			},
			message: $message,
		);
	}
	
	public function waitUntilEnabled(WebDriverBy|string $selector, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(BrowserManager $browser) use ($selector) {
				return $browser->resolver->findOrFail($selector)->isEnabled();
			},
			message: "Waited $seconds seconds for element to be enabled",
		);
	}
	
	public function waitUntilDisabled(WebDriverBy|string $selector, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(BrowserManager $browser) use ($selector) {
				return ! $browser->resolver->findOrFail($selector)->isEnabled();
			},
			message: "Waited $seconds seconds for element to be disabled",
		);
	}
	
	public function waitUntilVue(string $key, string $value, WebDriverBy|string $selector = null, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(BrowserManager $browser) use ($key, $value, $selector) {
				$element = $browser->resolver->findOrFail($selector);
				return $value === (new Vue($browser))->attribute($element, $key);
			},
			message: "Waited $seconds seconds for element to be disabled",
		);
	}
	
	public function waitUntilVueIsNot(string $key, string $value, WebDriverBy|string $selector = null, ?int $seconds = null): static
	{
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(BrowserManager $browser) use ($key, $value, $selector) {
				$element = $browser->resolver->findOrFail($selector);
				return $value !== (new Vue($browser))->attribute($element, $key);
			},
			message: "Waited $seconds seconds for element to be disabled",
		);
	}
	
	public function whenAvailable(WebDriverBy|string $selector, Closure $callback, ?int $seconds = null): static
	{
		// This is the best wait to check if a closure is static. Due to the nature of Dawn, closures
		// passed across the I/O channel have certain limitations, and forcing them to be static
		// helps ensure that they will work as expected. It's not ideal, but seems an OK solution for now.
		if (null !== @Closure::bind($callback, new stdClass())) {
			throw new InvalidArgumentException('Callbacks passed to whenAvailable() must be static.');
		}
		
		return $this->waitUsing(
			seconds: $seconds,
			interval: 100,
			wait: static function(BrowserManager $browser) use ($selector, $callback) {
				$element = $browser->resolver->findOrFail($selector);
				$callback($browser, $element);
			},
			message: 'Did not find selector before timeout.',
		);
	}
	
	public function waitForReload($callback = null, ?int $seconds = null): static
	{
		$token = Str::random();
		$this->executeScript("window['{$token}'] = 0;");
		
		if ($callback) {
			$callback($this);
		}
		
		return $this->waitUntil(
			script: "'undefined' === typeof window['{$token}']",
			seconds: $seconds,
			message: "Waited $seconds for page reload.",
		);
	}
	
	public function clickAndWaitForReload(WebDriverBy|string|null $selector = null, ?int $seconds = null): static
	{
		return $this->waitForReload(
			callback: static fn(Browser $browser) => $browser->click($selector),
			seconds: $seconds
		);
	}
	
	public function drag(WebDriverBy|string $from, WebDriverBy|string $to): static
	{
		return $this->dragAndDrop($from, $to);
	}
	
	public function dragUp(WebDriverBy|string $selector, int $offset): static
	{
		return $this->dragAndDropBy($selector, 0, abs($offset) * -1);
	}
	
	public function dragDown(WebDriverBy|string $selector, int $offset): static
	{
		return $this->dragAndDropBy($selector, 0, abs($offset));
	}
	
	public function dragLeft(WebDriverBy|string $selector, int $offset): static
	{
		return $this->dragAndDropBy($selector, abs($offset) * -1, 0);
	}
	
	public function dragRight(WebDriverBy|string $selector, int $offset): static
	{
		return $this->dragAndDropBy($selector, abs($offset), 0);
	}
	
	public function moveMouse(int $x, int $y): static
	{
		return $this->moveByOffset($x, $y);
	}
	
	public function clickAtXPath(string $expression): static
	{
		return $this->click(WebDriverBy::xpath($expression));
	}
	
	public function clickAtPoint(int $x, int $y): static
	{
		return $this->executeScript("document.elementFromPoint({$x}, {$y}).click()");
	}
	
	public function waitForLivewireToLoad(): static
	{
		return $this->waitUsing(6, 25, (new Livewire())->wait());
	}
	
	public function blank(): static
	{
		return $this->visit('about:blank');
	}
	
	public function scrollIntoView(string $selector): static
	{
		// TODO: Handle @dusk selectors and scoping
		
		return $this->executeScript('document.querySelector('.json_encode($selector).').scrollIntoView();');
	}
	
	public function scrollTo(string $selector): static
	{
		// TODO: Handle @dusk selectors and scoping
		
		return $this->executeScript(
			'document.querySelector('.json_encode($selector).').scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });'
		);
	}
	
	public function screenshot(string $name): static
	{
		// TODO:
		// Browser::$storeScreenshotsAt = base_path('tests/Browser/screenshots');
		
		return $this->takeScreenshot(base_path("tests/Browser/screenshots/{$name}.png"));
	}
	
	public function responsiveScreenshots(string $name): static
	{
		// TODO: $responsiveScreenSizes
		$sizes = [
			'xs' => [
				'width' => 360,
				'height' => 640,
			],
			'sm' => [
				'width' => 640,
				'height' => 360,
			],
			'md' => [
				'width' => 768,
				'height' => 1024,
			],
			'lg' => [
				'width' => 1024,
				'height' => 768,
			],
			'xl' => [
				'width' => 1280,
				'height' => 1024,
			],
			'2xl' => [
				'width' => 1536,
				'height' => 864,
			],
		];
		
		if (substr($name, -1) !== '/') {
			$name .= '-';
		}
		
		foreach ($sizes as $device => $size) {
			$this->resize($size['width'], $size['height'])->screenshot("$name$device");
		}
		
		return $this;
	}
	
	public function storeConsoleLog(string $name): static
	{
		// TODO: $storeConsoleLogAt
		return $this->getLog(base_path("tests/Browser/console/{$name}.log"));
	}
	
	public function storeSource(string $name): static
	{
		// TODO: 
		// Browser::$storeSourceAt = base_path('tests/Browser/source');
		
		if (! empty($source = $this->getPageSource())) {
			$fs = new Filesystem();
			$path = base_path("tests/Browser/source/{$name}.txt");
			
			$fs->ensureDirectoryExists(dirname($path));
			$fs->put($path, $source);
		}
		
		return $this;
	}
	
	public function withinFrame(string|WebDriverBy $selector, Closure $callback): static
	{
		try {
			$this->switchTo('frame', $selector);
			$callback($this);
		} finally {
			$this->switchTo('defaultContent');
		}
		
		return $this;
	}
	
	public function pause(int $milliseconds): static
	{
		return $this->sleep($milliseconds / 1000);
	}
	
	public function dump(): static
	{
		dump($this->getPageSource());
		
		return $this;
	}
	
	public function dd(): static
	{
		dd($this->getPageSource());
		
		/**
		 * We will never reach this, but it's useful to have for better IDE support.
		 *
		 * @noinspection PhpUnreachableStatementInspection
		 */
		return $this;
	}
}

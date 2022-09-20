<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

trait HasBrowserCommandAliases
{
	public function clickLink(string $text):static
	{
		return $this->click(WebDriverBy::linkText($link_text), $wait);
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
	
	public function quit(): static
	{
		return $this->quitBrowser();
	}
	
	public function press(WebDriverBy|string $selector, bool $wait = false): static
	{
		return $this->clickButton($selector, $wait);
	}
}

<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\ExecuteScript;
use Glhd\Dawn\Browser\Commands\QuitBrowser;
use Glhd\Dawn\Browser\Commands\SendKeys;
use Glhd\Dawn\Browser\Commands\TakeScreenshot;
use Glhd\Dawn\Browser\Commands\Visit;
use Glhd\Dawn\Browser\Commands\WaitForUrl;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesBrowserCommands
{
	public function executeScript(string|array $scripts): static
	{
		return $this->command(new ExecuteScript($scripts));
	}
	
	public function quitBrowser(): static
	{
		return $this->command(new QuitBrowser());
	}
	
	public function sendKeys(WebDriverBy|string $selector, string $keys, bool $clear_input = false, int $pause = 0): static
	{
		return $this->command(new SendKeys($selector, $keys, $clear_input, $pause));
	}
	
	public function takeScreenshot(string $filename): static
	{
		return $this->command(new TakeScreenshot($filename));
	}
	
	public function visit(string $url): static
	{
		return $this->command(new Visit($url));
	}
	
	public function waitForUrl(string $url, ?int $timeout = null, ?int $interval = null): static
	{
		return $this->command(new WaitForUrl($url, $timeout, $interval));
	}
}

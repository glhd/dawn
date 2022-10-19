<?php

namespace Glhd\Dawn\Browser\Concerns;

use Glhd\Dawn\Browser\Commands\ExecuteScript;
use Glhd\Dawn\Browser\Commands\QuitBrowser;
use Glhd\Dawn\Browser\Commands\TakeScreenshot;
use Glhd\Dawn\Browser\Commands\Visit;

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
	
	public function takeScreenshot(string $filename): static
	{
		return $this->command(new TakeScreenshot($filename));
	}
	
	public function visit(string $url): static
	{
		return $this->command(new Visit($url));
	}
}

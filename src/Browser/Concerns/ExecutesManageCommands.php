<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Manage\GetLog;
use Glhd\Dawn\Browser\Commands\Manage\GetPageSource;
use Glhd\Dawn\Browser\Commands\Manage\SwitchTo;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesManageCommands
{
	public function getLog(string $filename, string $log_type = 'browser'): static
	{
		return $this->command(new GetLog($filename, $log_type));
	}
	
	public function getPageSource(): ?string
	{
		return $this->command(new GetPageSource());
	}
	
	public function switchTo(string $locator, WebDriverBy|string|null $selector = null): static
	{
		return $this->command(new SwitchTo($locator, $selector));
	}
}

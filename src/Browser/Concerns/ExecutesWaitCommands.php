<?php

namespace Glhd\Dawn\Browser\Concerns;

use Closure;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\Commands\Wait\WaitForEvent;
use Glhd\Dawn\Browser\Commands\Wait\WaitForUrl;
use Glhd\Dawn\Browser\Commands\Wait\WaitUsing;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesWaitCommands
{
	public function waitForEvent(string $event, WebDriverBy|string|null $selector, ?int $seconds): static
	{
		return $this->command(new WaitForEvent($event, $selector, $seconds));
	}
	
	public function waitForUrl(string $url, ?int $timeout = null, ?int $interval = null): static
	{
		return $this->command(new WaitForUrl($url, $timeout, $interval));
	}
	
	public function waitUsing(?int $seconds, int $interval, Closure|WebDriverExpectedCondition $wait, ?string $message = null): static
	{
		return $this->command(new WaitUsing($seconds, $interval, $wait, $message));
	}
}

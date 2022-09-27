<?php

namespace Glhd\Dawn\Browser\Concerns;

use Closure;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\Commands\Wait\WaitUsing;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesWaitCommands
{
	public function waitUsing(?int $seconds, int $interval, Closure|WebDriverExpectedCondition $wait, ?string $message = null): static
	{
		return $this->command(new WaitUsing($seconds, $interval, $wait, $message));
	}
}

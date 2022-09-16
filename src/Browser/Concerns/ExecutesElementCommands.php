<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Elements\CheckOrUncheck;
use Glhd\Dawn\Browser\Commands\Elements\Click;
use Glhd\Dawn\Browser\Commands\Elements\ClickButton;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesElementCommands
{
	public function checkOrUncheck(WebDriverBy|string $selector, bool $check = true): static
	{
		return $this->command(new CheckOrUncheck($selector, $check));
	}
	
	public function click(WebDriverBy|string $selector, bool $wait = false): static
	{
		return $this->command(new Click($selector, $wait));
	}
	
	public function clickButton(WebDriverBy|string $selector, bool $wait = false): static
	{
		return $this->command(new ClickButton($selector, $wait));
	}
}

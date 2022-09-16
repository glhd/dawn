<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Mouse\MouseOver;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesMouseCommands
{
	public function mouseOver(WebDriverBy|string $by): static
	{
		return $this->command(new MouseOver($by));
	}
}

<?php

namespace Glhd\Dawn\Browser\Concerns;

use Glhd\Dawn\Browser\Commands\Navigate\Back;
use Glhd\Dawn\Browser\Commands\Navigate\Forward;
use Glhd\Dawn\Browser\Commands\Navigate\Refresh;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesNavigateCommands
{
	public function back(): static
	{
		return $this->command(new Back());
	}
	
	public function forward(): static
	{
		return $this->command(new Forward());
	}
	
	public function refresh(): static
	{
		return $this->command(new Refresh());
	}
}

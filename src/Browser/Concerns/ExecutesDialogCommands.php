<?php

namespace Glhd\Dawn\Browser\Concerns;

use Glhd\Dawn\Browser\Commands\Dialogs\AcceptDialog;
use Glhd\Dawn\Browser\Commands\Dialogs\DismissDialog;
use Glhd\Dawn\Browser\Commands\Dialogs\TypeInDialog;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesDialogCommands
{
	public function acceptDialog(): static
	{
		return $this->command(new AcceptDialog());
	}
	
	public function dismissDialog(): static
	{
		return $this->command(new DismissDialog());
	}
	
	public function typeInDialog(string $value): static
	{
		return $this->command(new TypeInDialog($value));
	}
}

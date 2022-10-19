<?php

namespace Glhd\Dawn\Browser\Concerns;

use Glhd\Dawn\Browser\Commands\Window\CloseBrowserWindow;
use Glhd\Dawn\Browser\Commands\Window\FitContent;
use Glhd\Dawn\Browser\Commands\Window\Maximize;
use Glhd\Dawn\Browser\Commands\Window\OpenNewWindow;
use Glhd\Dawn\Browser\Commands\Window\Resize;
use Glhd\Dawn\Browser\Commands\Window\SetPosition;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesWindowCommands
{
	public function closeBrowserWindow(): static
	{
		return $this->command(new CloseBrowserWindow());
	}
	
	public function fitContent(): static
	{
		return $this->command(new FitContent());
	}
	
	public function maximize(): static
	{
		return $this->command(new Maximize());
	}
	
	public function openNewWindow(): static
	{
		return $this->command(new OpenNewWindow());
	}
	
	public function resize(int $width, int $height): static
	{
		return $this->command(new Resize($width, $height));
	}
	
	public function setPosition(int $x, int $y): static
	{
		return $this->command(new SetPosition($x, $y));
	}
}

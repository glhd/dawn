<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Mouse\ClickAndHold;
use Glhd\Dawn\Browser\Commands\Mouse\DoubleClick;
use Glhd\Dawn\Browser\Commands\Mouse\DragAndDrop;
use Glhd\Dawn\Browser\Commands\Mouse\DragAndDropBy;
use Glhd\Dawn\Browser\Commands\Mouse\MouseOver;
use Glhd\Dawn\Browser\Commands\Mouse\MoveByOffset;
use Glhd\Dawn\Browser\Commands\Mouse\ReleaseMouse;
use Glhd\Dawn\Browser\Commands\Mouse\RightClick;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesMouseCommands
{
	public function clickAndHold(WebDriverBy|string|null $selector): static
	{
		return $this->command(new ClickAndHold($selector));
	}
	
	public function doubleClick(WebDriverBy|string|null $selector): static
	{
		return $this->command(new DoubleClick($selector));
	}
	
	public function dragAndDrop(WebDriverBy|string $from, WebDriverBy|string $to): static
	{
		return $this->command(new DragAndDrop($from, $to));
	}
	
	public function dragAndDropBy(WebDriverBy|string $selector, int $x = 0, int $y = 0): static
	{
		return $this->command(new DragAndDropBy($selector, $x, $y));
	}
	
	public function mouseOver(WebDriverBy|string $by): static
	{
		return $this->command(new MouseOver($by));
	}
	
	public function moveByOffset(int $x, int $y): static
	{
		return $this->command(new MoveByOffset($x, $y));
	}
	
	public function releaseMouse(): static
	{
		return $this->command(new ReleaseMouse());
	}
	
	public function rightClick(WebDriverBy|string|null $selector): static
	{
		return $this->command(new RightClick($selector));
	}
}

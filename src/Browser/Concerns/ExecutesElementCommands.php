<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Elements\CheckOrUncheck;
use Glhd\Dawn\Browser\Commands\Elements\Clear;
use Glhd\Dawn\Browser\Commands\Elements\Click;
use Glhd\Dawn\Browser\Commands\Elements\ClickRadio;
use Glhd\Dawn\Browser\Commands\Elements\GetAttribute;
use Glhd\Dawn\Browser\Commands\Elements\GetSelected;
use Glhd\Dawn\Browser\Commands\Elements\GetText;
use Glhd\Dawn\Browser\Commands\Elements\GetValue;
use Glhd\Dawn\Browser\Commands\Elements\Select;
use Glhd\Dawn\Browser\Commands\Elements\SendKeys;
use Glhd\Dawn\Browser\Commands\Elements\SetValue;

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
	
	public function clear(WebDriverBy|string $selector): static
	{
		return $this->command(new Clear($selector));
	}
	
	public function click(WebDriverBy|string|null $selector, string $resolver = 'findElement', bool $wait = false): static
	{
		return $this->command(new Click($selector, $resolver, $wait));
	}
	
	public function clickRadio(WebDriverBy|string $selector): static
	{
		return $this->command(new ClickRadio($selector));
	}
	
	/** @return $this|mixed */
	public function getAttribute(WebDriverBy|string $selector, string $attribute): mixed
	{
		return $this->command(new GetAttribute($selector, $attribute));
	}
	
	public function getSelected(WebDriverBy|string $selector, string $value): bool
	{
		return $this->command(new GetSelected($selector, $value));
	}
	
	public function getText(WebDriverBy|string $selector): string
	{
		return $this->command(new GetText($selector));
	}
	
	/** @return $this|mixed */
	public function getValue(WebDriverBy|string $selector): mixed
	{
		return $this->command(new GetValue($selector));
	}
	
	public function select(WebDriverBy|string $selector, string|array|null $value = null): static
	{
		return $this->command(new Select($selector, $value));
	}
	
	public function sendKeys(WebDriverBy|string $selector, string|array $keys, bool $clear_input = false, int $pause = 0): static
	{
		return $this->command(new SendKeys($selector, $keys, $clear_input, $pause));
	}
	
	public function setValue(WebDriverBy|string $selector, $value): static
	{
		return $this->command(new SetValue($selector, $value));
	}
}

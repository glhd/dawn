<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Assertions\AssertQueryStringHas;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSeeIn;
use Glhd\Dawn\Browser\Commands\Assertions\AssertTitle;
use Glhd\Dawn\Browser\Commands\Assertions\AssertTitleContains;
use Glhd\Dawn\Browser\Commands\Assertions\AssertUrlIs;
use PHPUnit\Framework\Assert;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesAssertionCommands
{
	public function assertQueryStringHas(string $name, $value = null): static
	{
		return $this->command(new AssertQueryStringHas($name, $value));
	}
	
	public function assertSeeIn(WebDriverBy|string $selector, string $expected): static
	{
		return $this->command(new AssertSeeIn($selector, $expected));
	}
	
	public function assertTitle(string $expected): static
	{
		return $this->command(new AssertTitle($expected));
	}
	
	public function assertTitleContains(string $expected): static
	{
		return $this->command(new AssertTitleContains($expected));
	}
	
	public function assertUrlIs(string $expected): static
	{
		return $this->command(new AssertUrlIs($expected));
	}
}

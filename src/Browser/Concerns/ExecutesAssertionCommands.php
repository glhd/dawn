<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\Cookie;
use Glhd\Dawn\Browser\Commands\Assertions\AssertCookieMissing;
use Glhd\Dawn\Browser\Commands\Assertions\AssertDialogOpened;
use Glhd\Dawn\Browser\Commands\Assertions\AssertHasCookie;
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
	public function assertCookieMissing(string $name, bool $decrypt = true): static
	{
		return $this->command(new AssertCookieMissing($name, $decrypt));
	}
	
	public function assertDialogOpened(?string $expected = null): static
	{
		return $this->command(new AssertDialogOpened($expected));
	}
	
	public function assertHasCookie(string $name, ?string $expected = null, bool $decrypt = true): static
	{
		return $this->command(new AssertHasCookie($name, $expected, $decrypt));
	}
	
	public function assertQueryStringHas(string $name, $value = null): static
	{
		return $this->command(new AssertQueryStringHas($name, $value));
	}
	
	public function assertSeeIn(string $selector, string $expected): static
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

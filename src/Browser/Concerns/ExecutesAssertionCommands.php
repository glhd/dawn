<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Glhd\Dawn\Browser\Commands\Assertions\AssertCookieMissing;
use Glhd\Dawn\Browser\Commands\Assertions\AssertDialogOpened;
use Glhd\Dawn\Browser\Commands\Assertions\AssertDontSeeIn;
use Glhd\Dawn\Browser\Commands\Assertions\AssertElementExistence;
use Glhd\Dawn\Browser\Commands\Assertions\AssertHasCookie;
use Glhd\Dawn\Browser\Commands\Assertions\AssertInputValue;
use Glhd\Dawn\Browser\Commands\Assertions\AssertLinkVisibility;
use Glhd\Dawn\Browser\Commands\Assertions\AssertOptionPresence;
use Glhd\Dawn\Browser\Commands\Assertions\AssertOptionSelectionState;
use Glhd\Dawn\Browser\Commands\Assertions\AssertQueryStringHas;
use Glhd\Dawn\Browser\Commands\Assertions\AssertScript;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSeeAnythingIn;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSeeIn;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSeeNothingIn;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSelectionState;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSourceHas;
use Glhd\Dawn\Browser\Commands\Assertions\AssertSourceMissing;
use Glhd\Dawn\Browser\Commands\Assertions\AssertTitle;
use Glhd\Dawn\Browser\Commands\Assertions\AssertTitleContains;
use Glhd\Dawn\Browser\Commands\Assertions\AssertUrlIs;
use Glhd\Dawn\Browser\Commands\Assertions\AssertValue;
use Illuminate\Support\Str;
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
	
	public function assertDontSeeIn(string|WebDriverBy $selector, string $needle): static
	{
		return $this->command(new AssertDontSeeIn($selector, $needle));
	}
	
	public function assertElementExistence(WebDriverBy|string $selector, bool $expected = true): static
	{
		return $this->command(new AssertElementExistence($selector, $expected));
	}
	
	public function assertHasCookie(string $name, ?string $expected = null, bool $decrypt = true): static
	{
		return $this->command(new AssertHasCookie($name, $expected, $decrypt));
	}
	
	public function assertInputValue(WebDriverBy|string $selector, $value, bool $not = false): static
	{
		return $this->command(new AssertInputValue($selector, $value, $not));
	}
	
	public function assertLinkVisibility(string $text, bool $expected = true, bool $partial = false): static
	{
		return $this->command(new AssertLinkVisibility($text, $expected, $partial));
	}
	
	public function assertOptionPresence(WebDriverBy|string $selector, array $options, bool $expected = true, string $message = ''): static
	{
		return $this->command(new AssertOptionPresence($selector, $options, $expected, $message));
	}
	
	public function assertOptionSelectionState(WebDriverBy|string $selector, $value, bool $expected = true, string $message = ''): static
	{
		return $this->command(new AssertOptionSelectionState($selector, $value, $expected, $message));
	}
	
	public function assertQueryStringHas(string $name, $value = null): static
	{
		return $this->command(new AssertQueryStringHas($name, $value));
	}
	
	public function assertScript(string $expression, $expected = true): static
	{
		return $this->command(new AssertScript($expression, $expected));
	}
	
	public function assertSeeAnythingIn(string|WebDriverBy $selector): static
	{
		return $this->command(new AssertSeeAnythingIn($selector));
	}
	
	public function assertSeeIn(string|WebDriverBy $selector, string $needle): static
	{
		return $this->command(new AssertSeeIn($selector, $needle));
	}
	
	public function assertSeeNothingIn(string|WebDriverBy $selector): static
	{
		return $this->command(new AssertSeeNothingIn($selector));
	}
	
	public function assertSelectionState(WebDriverBy|string $selector, $value, bool $expected = true, bool $expect_indeterminate = false, string $resolver = 'findOrFail', string $message = ''): static
	{
		return $this->command(new AssertSelectionState($selector, $value, $expected, $expect_indeterminate, $resolver, $message));
	}
	
	public function assertSourceHas(string $needle): static
	{
		return $this->command(new AssertSourceHas($needle));
	}
	
	public function assertSourceMissing(string $needle): static
	{
		return $this->command(new AssertSourceMissing($needle));
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
	
	public function assertValue(WebDriverBy|string $selector, $value, bool $not = false): static
	{
		return $this->command(new AssertValue($selector, $value, $not));
	}
}

<?php

namespace Glhd\Dawn\Browser\Concerns;

use DateTimeInterface;
use Glhd\Dawn\Browser\Commands\Cookies\AddCookie;
use Glhd\Dawn\Browser\Commands\Cookies\DeleteAllCookies;
use Glhd\Dawn\Browser\Commands\Cookies\DeleteCookie;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesCookieCommands
{
	public function addCookie(string $name, ?string $value = null, int|DateTimeInterface|null $expiry = null, array $options = [], bool $encrypt = true): static
	{
		return $this->command(new AddCookie($name, $value, $expiry, $options, $encrypt));
	}
	
	public function deleteAllCookies(): static
	{
		return $this->command(new DeleteAllCookies());
	}
	
	public function deleteCookie(string $name): static
	{
		return $this->command(new DeleteCookie($name));
	}
}

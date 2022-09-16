<?php

namespace Glhd\Dawn\Browser\Concerns;

use DateTimeInterface;
use Glhd\Dawn\Browser\Commands\Cookies\DeleteAllCookies;
use Glhd\Dawn\Browser\Commands\Cookies\SetCookie;

/**
 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
 *
 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
 */
trait ExecutesCookieCommands
{
	public function deleteAllCookies(): static
	{
		return $this->command(new DeleteAllCookies());
	}
	
	public function setCookie(string $name, ?string $value = null, int|DateTimeInterface|null $expiry = null, array $options = [], bool $encrypt = true): static
	{
		return $this->command(new SetCookie($name, $value, $expiry, $options, $encrypt));
	}
}

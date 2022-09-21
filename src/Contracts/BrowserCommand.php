<?php

namespace Glhd\Dawn\Contracts;

use Glhd\Dawn\Browser;

interface BrowserCommand
{
	public function setBrowser(Browser $browser): static;
}

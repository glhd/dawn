<?php

namespace Glhd\Dawn\Browser\Commands\Concerns;

use Glhd\Dawn\Browser;

trait InteractsWithBrowserInstance
{
	public string $browser_id;
	
	public function setBrowser(Browser $browser): static
	{
		$this->browser_id = $browser->id;
		
		return $this;
	}
}

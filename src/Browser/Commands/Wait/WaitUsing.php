<?php

namespace Glhd\Dawn\Browser\Commands\Wait;

use Closure;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Laravel\SerializableClosure\SerializableClosure;

class WaitUsing extends BrowserCommand
{
	public SerializableClosure $closure;
	
	public function __construct(
		public ?int $seconds,
		public int $interval,
		Closure|WebDriverExpectedCondition $wait,
		public ?string $message = null,
	) {
		if ($wait instanceof WebDriverExpectedCondition) {
			$wait = $wait->getApply();
		}
		
		$this->closure = new SerializableClosure($wait->bindTo(null));
		$this->seconds ??= 5;
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->wait($this->seconds, $this->interval)
			->until($this->getClosure(), $this->message);
	}
	
	protected function getClosure(): Closure
	{
		return $this->closure->getClosure();
	}
}

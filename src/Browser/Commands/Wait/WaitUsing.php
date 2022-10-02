<?php

namespace Glhd\Dawn\Browser\Commands\Wait;

use Closure;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Illuminate\Support\Traits\ReflectsClosures;
use Laravel\SerializableClosure\SerializableClosure;

class WaitUsing extends BrowserCommand
{
	use ReflectsClosures;
	
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
			->until($this->getClosure($manager), $this->message);
	}
	
	protected function getClosure(BrowserManager $manager): Closure
	{
		$closure = $this->closure->getClosure();
		
		// If our wait callback needs access to the BrowserManager instance, we'll need
		// to wrap it in a native "until" callback because that's handled inside the webdriver package.
		if (BrowserManager::class === $this->firstClosureParameterType($closure)) {
			return static function() use ($closure, $manager) {
				return $closure($manager);
			};
		}
		
		return $closure;
	}
}

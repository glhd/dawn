<?php

namespace Glhd\Dawn\Browser;

use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class SeleniumDriverProcess extends Process
{
	public function __construct(?string $executable = null, int $port = 9515, array $arguments = [])
	{
		$executable ??= $this->findChromeDriverExecutable();
		
		array_unshift($arguments, "--port={$port}");
		array_unshift($arguments, $executable);
		
		parent::__construct(command: $arguments, env: $this->getSeleniumDriverEnvironment());
		
		register_shutdown_function(fn() => $this->signal(SIGKILL));
		
		$this->start();
		
		$this->waitUntil(function($type, $output) {
			return Str::of($output)->contains('started successfully');
		});
	}
	
	protected function findChromeDriverExecutable(): string
	{
		$finder = new ExecutableFinder();
		
		$default = file_exists('/opt/homebrew/bin/chromedriver')
			? '/opt/homebrew/bin/chromedriver'
			: '/usr/local/bin/chromedriver';
		
		return $finder->find('chromedriver', $default);
	}
	
	protected function getSeleniumDriverEnvironment(): array
	{
		if ($this->onMacOrWindows()) {
			return [];
		}
		
		return [
			'DISPLAY' => $_ENV['DISPLAY'] ?? ':0',
		];
	}
	
	protected function onMacOrWindows(): bool
	{
		return PHP_OS === 'Darwin'
			|| PHP_OS === 'WINNT'
			|| Str::contains(php_uname(), 'Microsoft');
	}
}

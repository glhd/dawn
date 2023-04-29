<?php

namespace Glhd\Dawn\Providers;

use Glhd\Dawn\Browser;
use Glhd\Dawn\Browser\RemoteWebDriverBroker;
use Glhd\Dawn\Browser\SeleniumDriverProcess;
use Glhd\Dawn\Console\Commands\DriveCommand;
use Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand;
use Glhd\Dawn\Console\Commands\ServeCommand;
use Glhd\Dawn\Http\WebServerBroker;
use Glhd\Dawn\Support\Debugger;
use Glhd\Dawn\Support\ProcessManager;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Loop;

class DawnServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom($this->packageConfigFile(), 'dawn');

		if (! $this->app->runningUnitTests()) {
			return;
		}
		
		$this->app->singleton('dawn.loop', function() {
			return Loop::get();
		});
		
		$this->app->singleton(Debugger::class, function() {
			return match (config('dawn.debugger')) {
				'dump' => new Debugger(fn($message) => dump($message)),
				'ray' => new Debugger(fn($message) => ray($message)),
				'log' => new Debugger(fn($message) => Log::debug($message)),
				default => new Debugger(),
			};
		});
		
		$this->app->bind(WebServerBroker::class, function(Container $app) {
			return $app->make(ProcessManager::class)->web_server;
		});
		
		$this->app->bind(RemoteWebDriverBroker::class, function(Container $app) {
			return $app->make(ProcessManager::class)->remote_web_driver;
		});
		
		$this->app->singleton(SeleniumDriverProcess::class, function() {
			return new SeleniumDriverProcess(port: $this->seleniumPort());
		});
		
		$this->app->bind(ProcessManager::class, function() {
			// Under the hood, Dawn manages its own singleton instance so that the same
			// processes can be shared across multiple tests. We pass the current Dawn
			// config in each time to ensure that if config values have been changed
			// dynamically, new processes can be spawned.
			return ProcessManager::getInstance(config('dawn'));
		});
		
		$this->app->bind(Browser::class, function(Container $app) {
			// When we ask for a browser, we want all background processes running,
			// so we'll load up the full process manager (even though the browser
			// only really cares about the webdriver process).
			return new Browser(
				broker: $app->make(ProcessManager::class)->remote_web_driver,
				loop: $app->make('dawn.loop'),
			);
		});
	}
	
	public function boot()
	{
		$this->publishes(
			[$this->packageConfigFile() => $this->app->configPath('dawn.php')],
			['dawn', 'dawn-config']
		);

		Blade::directive('dawnTarget', function($expression) {
			$attribute = config('dawn.target_attribute', 'data-dawn-target');
			
			if (empty($attribute)) {
				return '';
			}
			
			return '<?php echo \' '.$attribute.'="\'.e((string) '.$expression.').\'" \'; ?>';
		});
		
		if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
			$this->commands([
				DriveCommand::class,
				GenerateCommandHelpersCommand::class,
				ServeCommand::class,
			]);
		}
	}
	
	protected function seleniumPort(): int
	{
		$port = parse_url(config('dawn.browser_url', 'http://localhost:9515'), PHP_URL_PORT);
		
		if (is_numeric($port)) {
			return (int) $port;
		}
		
		return 9515;
	}

	protected function packageConfigFile(): string
	{
		return __DIR__.'/../../config/dawn.php';
	}
}

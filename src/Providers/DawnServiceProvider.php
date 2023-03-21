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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Loop;

class DawnServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/../../config/dawn.php', 'dawn');
		
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
		
		$this->app->singleton(WebServerBroker::class, function() {
			return new WebServerBroker(
				host: config('dawn.server_host', '127.0.0.1'),
				port: config('dawn.server_port') ?? $this->findOpenPort(),
			);
		});
		
		$this->app->singleton(RemoteWebDriverBroker::class, function() {
			return new RemoteWebDriverBroker(config('dawn.browser_url', 'http://localhost:9515'));
		});
		
		$this->app->singleton(SeleniumDriverProcess::class, function() {
			return new SeleniumDriverProcess(port: $this->seleniumPort());
		});
		
		$this->app->singleton(ProcessManager::class, function(Container $app) {
			return new ProcessManager(
				remote_web_driver: $app->make(RemoteWebDriverBroker::class),
				web_server: $app->make(WebServerBroker::class),
			);
		});
		
		$this->app->bind(Browser::class, function(Container $app) {
			// When we ask for a browser, we want all background processes running,
			// so we'll load up the full process manager (even though the browser
			// only really cares about the webdriver process). We'll do this thru
			// an internal singleton, so that the processes run across all tests,
			// regardless of the number of times the application is bootstrapped
			$pm = ProcessManager::getInstance();
			
			return new Browser(
				broker: $pm->remote_web_driver,
				loop: $app->make('dawn.loop'),
			);
		});
	}
	
	public function boot()
	{
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
	
	protected function findOpenPort(): int
	{
		$sock = socket_create_listen(0);
		
		socket_getsockname($sock, $addr, $port);
		socket_close($sock);
		
		return $port;
	}
	
	protected function seleniumPort(): int
	{
		$port = parse_url(config('dawn.browser_url', 'http://localhost:9515'), PHP_URL_PORT);
		
		if (is_numeric($port)) {
			return (int) $port;
		}
		
		return 9515;
	}
}

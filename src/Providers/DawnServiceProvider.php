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
				default => new Debugger(),
			};
		});
		
		$this->app->singleton(WebServerBroker::class, function() {
			return new WebServerBroker(
				host: config('dawn.server.host', '127.0.0.1'),
				port: config('dawn.server.port') ?? $this->findOpenPort(),
			);
		});
		
		$this->app->singleton(RemoteWebDriverBroker::class, function() {
			return new RemoteWebDriverBroker(config('dawn.browser.url', 'http://localhost:9515'));
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
			// only really cares about the webdriver process).
			$pm = $app->make(ProcessManager::class);
			
			return new Browser(
				broker: $pm->remote_web_driver,
				loop: $app->make('dawn.loop'),
			);
		});
	}
	
	public function boot()
	{
		Blade::directive('dawnTarget', function($expression) {
			return App::runningUnitTests()
				? '<?php echo \' data-dawn-target="\'.e((string) '.$expression.').\'" \'; ?>'
				: '';
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
		$port = parse_url(config('dawn.browser.url', 'http://localhost:9515'), PHP_URL_PORT);
		
		if (is_numeric($port)) {
			return (int) $port;
		}
		
		return 9515;
	}
}

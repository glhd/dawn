<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Web Server
	|--------------------------------------------------------------------------
	|
	| Configure the host and port to relay Dawn requests through. If you leave
	| the port as NULL, Dawn will find an open port automatically.
	|
	*/
	'server_host' => '127.0.0.1',
	'server_port' => null,
	
	/*
	|--------------------------------------------------------------------------
	| WebDriver
	|--------------------------------------------------------------------------
	|
	| Configure the Chrome/Selenium WebDriver setup.
	|
	*/
	'browser_url' => 'http://localhost:9515',
	'browser_window' => '1200,720',
	'browser_headless' => false,
	'browser_sandbox' => null === env('CI'),
	
	/*
	|--------------------------------------------------------------------------
	| Storage
	|--------------------------------------------------------------------------
	|
	| Configure where Dawn should store screenshots/logs/etc by default.
	|
	*/
	'storage_screenshots' => resource_path('dawn/screenshots'),
	'storage_logs' => resource_path('dawn/logs'),
	'storage_sources' => resource_path('dawn/sources'),
	
	/*
	|--------------------------------------------------------------------------
	| DOM Targeting
	|--------------------------------------------------------------------------
	|
	| Configure the attribute Dawn uses for @dawnTarget() calls. This is useful
	| if you have other code that depends on `dusk=` attributes, or you want
	| to re-use targets for other systems, like `data-intercom-target` for 
	| Intercom product tours, or `data-cy` or `data-testid` for Cypress tests.
	|
	*/
	'target_attribute' => 'data-dawn-target',
	
	/*
	|--------------------------------------------------------------------------
	| Debugger
	|--------------------------------------------------------------------------
	|
	| Available debuggers: null (no debugging), 'dump' (dump debug messages to
	| standard output), 'log' (send messages to debug log), or 'ray' (send 
	| debug messages to Ray <https://myray.app>).
	|
	*/
	'debugger' => null,
];

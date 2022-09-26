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
	'server' => [
		'host' => '127.0.0.1',
		'port' => null,
	],
	
	/*
	|--------------------------------------------------------------------------
	| WebDriver
	|--------------------------------------------------------------------------
	|
	| Configure the Chrome/Selenium WebDriver setup.
	|
	*/
	'browser' => [
		'url' => 'http://localhost:9515',
		'window' => '1200,720',
		'headless' => false,
		'sandbox' => null === env('CI'),
	],
	
	/*
	|--------------------------------------------------------------------------
	| Debugger
	|--------------------------------------------------------------------------
	|
	| Available debuggers: null (no debugging), 'dump' (dump debug messages to
	| standard output), or 'ray' (send debug messages to Ray).
	|
	*/
	'debugger' => null,
];

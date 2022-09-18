<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Web Server
	|--------------------------------------------------------------------------
	|
	| Configure the host and port to relay Dawn requests through.
	|
	*/
	'server' => [
		'host' => '127.0.0.1',
		'port' => 8089,
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
		'window' => '1920,1080',
		'headless' => false,
		'sandbox' => ! env('CI', false),
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

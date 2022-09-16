<?php

namespace Glhd\Dawn\Http;

use Glhd\Dawn\Support\BackgroundProcess;
use Glhd\Dawn\Support\LocalHttpCommandRelay;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

class WebServerProcess extends BackgroundProcess
{
	public LocalHttpCommandRelay $relay;
	
	public function __construct(
		?string $public_path = null,
		string $host = '127.0.0.1',
		int $port = 8089,
		?LoopInterface $loop = null,
		?ReadableStreamInterface $stdin = null,
		?WritableStreamInterface $stdout = null,
	) {
		parent::__construct($loop, $stdin, $stdout);
		
		$this->relay = new LocalHttpCommandRelay(
			loop: $this->loop,
			io: $this->io,
			public_path: $public_path ?? getcwd(),
			host: $host,
			port: $port,
		);
		
		$this->sendNotice('HTTP server is running.');
	}
	
	public function stop(): void
	{
		$this->relay->stop();
	}
}

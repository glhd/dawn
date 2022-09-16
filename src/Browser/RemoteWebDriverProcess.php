<?php

namespace Glhd\Dawn\Browser;

use Closure;
use Glhd\Dawn\Browser\Commands\SendRemoteWebDriverResponse;
use Glhd\Dawn\Concerns\SendsAndReceivesCommands;
use Glhd\Dawn\IO\Command;
use Glhd\Dawn\Support\BackgroundProcess;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

class RemoteWebDriverProcess extends BackgroundProcess
{
	use SendsAndReceivesCommands;
	
	public BrowserManager $browser_manager;
	
	public function __construct(
		Closure|string $connect,
		?LoopInterface $loop = null,
		?ReadableStreamInterface $stdin = null,
		?WritableStreamInterface $stdout = null,
	) {
		parent::__construct($loop, $stdin, $stdout);
		
		$this->browser_manager = new BrowserManager($connect);
		
		$this->sendNotice('Web driver server is running.');
	}
	
	public function respond(Command $request, $response = null): static
	{
		return $this->sendCommand(new SendRemoteWebDriverResponse($request->id, $response));
	}
	
	protected function stop(): void
	{
		$this->sendNotice('Stopping web driver server...');
		
		$this->browser_manager->quitAll();
	}
}

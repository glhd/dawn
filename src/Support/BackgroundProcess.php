<?php

namespace Glhd\Dawn\Support;

use Glhd\Dawn\Concerns\SendsAndReceivesCommands;
use Glhd\Dawn\IO\CommandIO;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;

abstract class BackgroundProcess
{
	use SendsAndReceivesCommands;
	
	public function __construct(
		protected ?LoopInterface $loop = null,
		?ReadableStreamInterface $stdin = null,
		?WritableStreamInterface $stdout = null,
	) {
		$this->loop ??= Loop::get();
		
		$stdin ??= new ReadableResourceStream(STDIN, $this->loop);
		$stdout ??= new WritableResourceStream(STDOUT, $this->loop);
		
		$this->io = new CommandIO($this, $stdin, $stdout);
		
		$this->loop->addSignal(SIGTERM, fn() => $this->stop());
	}
	
	protected function stop(): void
	{
	}
}

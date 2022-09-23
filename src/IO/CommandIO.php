<?php

namespace Glhd\Dawn\IO;

use Glhd\Dawn\Support\BackgroundProcess;
use Glhd\Dawn\Support\Broker;
use Illuminate\Support\Reflector;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use ReflectionMethod;
use Throwable;

class CommandIO
{
	protected CommandBuffer $buffer;
	
	public function __construct(
		protected Broker|BackgroundProcess $context,
		ReadableStreamInterface $in,
		protected WritableStreamInterface $out,
	) {
		$this->buffer = new CommandBuffer($this->handleCommand(...));
		$in->on('data', fn($chunk) => $this->buffer->write($chunk));
	}
	
	public function sendCommand(Command $command): static
	{
		$this->out->write($command->toData());
		
		return $this;
	}
	
	protected function handleCommand(Command $command): void
	{
		if (! method_exists($command, 'execute')) {
			return;
		}
		
		$reflection = new ReflectionMethod($command, 'execute');
		if (! count($parameters = $reflection->getParameters())) {
			return;
		}
		
		foreach (Reflector::getParameterClassNames($parameters[0]) as $type_hint) {
			if (is_a($this->context, $type_hint)) {
				try {
					$command->execute($this->context);
				} catch (Throwable $exception) {
					if ($this->context instanceof BackgroundProcess) {
						$this->context->sendException($exception);
					} else {
						throw $exception;
					}
				}
				return;
			}
		}
	}
}

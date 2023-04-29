<?php

namespace Glhd\Dawn\Support;

use Illuminate\Support\Collection;
use React\EventLoop\LoopInterface;
use RuntimeException;
use Throwable;

class ResponseQueue
{
	protected Collection $queue;
	
	public function __construct(
		protected LoopInterface $loop,
	) {
		$this->queue = new Collection();
	}
	
	public function push(string $request_id, $response): static
	{
		$this->queue->put($request_id, $response);
		
		return $this;
	}
	
	public function waitForResponse(string $request_id, float $timeout = 10): mixed
	{
		$this->loop->addPeriodicTimer(0.1, function($timer) use ($request_id) {
			if ($this->queue->has($request_id)) {
				$this->loop->cancelTimer($timer);
				$this->loop->stop();
			}
		});
		
		$timed_out = false;
		$this->loop->addTimer($timeout, function() use (&$timed_out) {
			$timed_out = true;
			$this->loop->stop();
		});
		
		while (! $this->queue->has($request_id)) {
			if ($timed_out) {
				throw new RuntimeException('Background process timed out.');
			}
			$this->loop->run();
		}
		
		$response = $this->queue->pull($request_id);
		
		if ($response instanceof Throwable) {
			throw $response;
		}
		
		return $response;
	}
}

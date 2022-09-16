<?php

namespace Glhd\Dawn\Support;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Glhd\Dawn\Concerns\SendsAndReceivesCommands;
use Glhd\Dawn\Http\Commands\HandleWebRequest;
use Glhd\Dawn\IO\CommandIO;
use Glhd\Dawn\IO\Commands\ThrowException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer as ReactHttpServer;
use React\Http\Message\Response;
use React\Promise\Promise;
use React\Socket\SocketServer;
use React\Stream\ReadableResourceStream;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class LocalHttpCommandRelay
{
	use SendsAndReceivesCommands;
	
	public bool $running = true;
	
	protected Collection $queue;
	
	protected SocketServer $socket;
	
	public function __construct(
		protected LoopInterface $loop,
		protected CommandIO $io,
		protected ?string $public_path = null,
		string $host = '127.0.0.1',
		int $port = 8089,
	) {
		$this->public_path ??= getcwd();
		$this->queue = new Collection();
		$this->socket = $this->getSocketServer($host, $port, $this->loop);
	}
	
	public function handleResponse(string $request_id, Response $response): void
	{
		// If we don't have a handler queued for this message ID, we'll just abort
		if (! $resolve = $this->queue->pull($request_id)) {
			$this->sendCommand(ThrowException::runtime("There is no handler for request '{$request_id}'"));
			return;
		}
		
		$resolve($response);
	}
	
	public function stop(): void
	{
		// Clear out remaining queue
		while ($resolve = $this->queue->shift()) {
			$resolve(Response::plaintext('Shutting down at '.microtime(true))->withStatus(500));
		}
		
		$this->socket->close();
	}
	
	protected function getSocketServer(string $host, int $port, LoopInterface $loop): SocketServer
	{
		$socket = new SocketServer("{$host}:{$port}", [], $loop);
		
		$http = new ReactHttpServer($loop, $this->handleRequest(...));
		$http->listen($socket);
		
		return $socket;
	}
	
	protected function handleRequest(ServerRequestInterface $psr_request): Promise|Response
	{
		// If this is just a request for a static asset, just stream that content back
		if ($static_response = $this->staticResponse($psr_request)) {
			return $static_response;
		}
		
		$this->sendCommand($message = new HandleWebRequest($psr_request));
		
		return $this->deferredResponse($message->id);
	}
	
	protected function deferredResponse(string $id): Promise
	{
		$promise = new Promise(function($resolve) use ($id) {
			$this->queue->put($id, $resolve);
			
			// 30 second timeout
			Loop::addTimer(30, function() use ($id, $resolve) {
				if ($this->queue->pull($id, false)) {
					$resolve($this->timeout());
				}
			});
		});
		
		// Handle exception
		$promise->otherwise(fn(Throwable $exception) => $this->internalError($exception));
		
		return $promise;
	}
	
	protected function staticResponse(ServerRequestInterface $psr_request): ?Response
	{
		$path = $psr_request->getUri()->getPath();
		
		if (Str::contains($path, '../')) {
			return null;
		}
		
		$filepath = $this->public_path.'/'.ltrim($path, '/');
		
		if (file_exists($filepath) && ! is_dir($filepath)) {
			return new Response(
				status: 200,
				headers: [
					'Content-Type' => match (pathinfo($filepath, PATHINFO_EXTENSION)) {
						'css' => 'text/css',
						'js' => 'application/javascript',
						'png' => 'image/png',
						'jpg', 'jpeg' => 'image/jpeg',
						'svg' => 'image/svg+xml',
						'woff' => 'font/woff',
						'woff2' => 'font/woff2',
						'eot' => 'application/vnd.ms-fontobject',
						'ttf' => 'font/ttf',
						default => (new MimeTypes())->guessMimeType($filepath),
					},
				],
				body: new ReadableResourceStream(fopen($filepath, 'r')),
			);
		}
		
		return null;
	}
	
	protected function timeout(): ResponseInterface
	{
		return Response::plaintext('Request timed out.')
			->withStatus(Response::STATUS_GATEWAY_TIMEOUT);
	}
	
	protected function internalError(?Throwable $exception): ResponseInterface
	{
		$exception ??= new Exception('Internal error');
		
		return Response::plaintext((string) $exception)
			->withStatus(Response::STATUS_INTERNAL_SERVER_ERROR);
	}
}

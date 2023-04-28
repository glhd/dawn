<?php

namespace Glhd\Dawn\Http\Commands;

use Glhd\Dawn\Http\Commands\SendWebResponse as ResponseMessage;
use Glhd\Dawn\Http\WebServerBroker;
use Glhd\Dawn\IO\Command;
use Glhd\Dawn\Support\CachedBodyStream;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class HandleWebRequest extends Command
{
	public function __construct(
		public ServerRequestInterface $request,
		LoopInterface $loop
	) {
		$this->id ??= (string) Str::uuid();
		
		$this->saveBodyToFilesystem($loop);
	}
	
	public function execute(WebServerBroker $broker)
	{
		try {
			$request = Request::createFromBase((new HttpFoundationFactory())->createRequest($this->request));
			
			$response = $this->runRequestThroughKernel($request);
			
			$broker->debug("[{$response->getStatusCode()}] {$request->url()}");
			
			$broker->sendCommand(ResponseMessage::from($this->id, $response));
		} catch (HttpException $exception) {
			$broker->sendCommand(ResponseMessage::from($this->id, response(
				content: $exception->getMessage(),
				status: $exception->getStatusCode(),
				headers: $exception->getHeaders(),
			)));
		}
	}
	
	protected function saveBodyToFilesystem(LoopInterface $loop): void
	{
		$body = $this->request->getBody();
		$size = $body->getSize();
		
		if ($size < 250) {
			return;
		}
		
		$filename = tempnam(sys_get_temp_dir(), 'dawn');
		$handle = fopen($filename, 'w+');
		
		$this->request = $this->request->withBody(new CachedBodyStream($filename));
		
		$body->on('data', function($data) use (&$handle) {
			fwrite($handle, $data);
		});
		
		$body->on('end', function() use ($loop, $filename, &$handle) {
			fclose($handle);
			$handle = null;
			$loop->stop();
		});
		
		$body->on('error', function(Throwable $e) {
			throw $e;
		});
		
		while($handle) {
			$loop->run();
		}
	}
	
	protected function runRequestThroughKernel(Request $request): Response
	{
		$kernel = app(HttpKernel::class);
		
		$response = $kernel->handle($request);
		
		$kernel->terminate($request, $response);
		
		return $response;
	}
}

<?php

namespace Glhd\Dawn\Http\Commands;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Glhd\Dawn\Http\Commands\SendWebResponse as ResponseMessage;
use Glhd\Dawn\Http\WebServerBroker;
use Glhd\Dawn\IO\Command;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HandleWebRequest extends Command
{
	public function __construct(
		public ServerRequestInterface $request,
	) {
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
	
	protected function runRequestThroughKernel(Request $request): Response
	{
		$kernel = app(HttpKernel::class);
		
		$response = $kernel->handle($request);
		
		$kernel->terminate($request, $response);
		
		return $response;
	}
}

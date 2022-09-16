<?php

namespace Glhd\Dawn\Http\Commands;

use Glhd\Dawn\Http\WebServerProcess;
use Glhd\Dawn\IO\Command;
use React\Http\Message\Response as ReactResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SendWebResponse extends Command
{
	public static function from(string $id, SymfonyResponse $response): self
	{
		return new self(
			$id,
			$response->getStatusCode(),
			static::getPsrHeaders($response),
			$response->getContent(),
			$response->getProtocolVersion(),
		);
	}
	
	protected static function getPsrHeaders(SymfonyResponse $response): array
	{
		$headers = $response->headers->all();
		
		if (! empty($cookies = $response->headers->getCookies())) {
			$headers['Set-Cookie'] = [];
			foreach ($cookies as $cookie) {
				$headers['Set-Cookie'][] = $cookie->__toString();
			}
		}
		
		return $headers;
	}
	
	public function __construct(
		public string $request_id,
		public int $status,
		public array $headers,
		public string $content,
		public string $version,
	) {
	}
	
	public function execute(WebServerProcess $server)
	{
		$server->relay->handleResponse(
			request_id: $this->request_id,
			response: new ReactResponse($this->status, $this->headers, $this->content, $this->version),
		);
	}
}

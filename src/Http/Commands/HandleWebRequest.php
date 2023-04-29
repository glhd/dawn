<?php

namespace Glhd\Dawn\Http\Commands;

use Glhd\Dawn\Http\Commands\SendWebResponse as ResponseMessage;
use Glhd\Dawn\Http\WebServerBroker;
use Glhd\Dawn\IO\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HandleWebRequest extends Command
{
	public array $files = [];
	
	public function __construct(public ServerRequestInterface $request)
	{
		$this->id ??= (string) Str::uuid();
		
		// Passing large uploads over the I/O channel can lead to memory issues. This just
		// puts the file in a temp location and then restores it on the other side.
		$this->saveFilesToFilesystem();
	}
	
	public function execute(WebServerBroker $broker)
	{
		try {
			$request = Request::createFromBase((new HttpFoundationFactory())->createRequest($this->request));
			
			$request->files->replace($this->getFilesFromFilesystem());
			
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
	
	protected function getFilesFromFilesystem(): array
	{
		return collect($this->files)
			->map(function(array $data) {
				return new UploadedFile(
					$data['path'],
					$data['original_filename'],
					$data['mime'],
					$data['error'],
				);
			})
			->all();
	}
	
	protected function saveFilesToFilesystem(): void
	{
		$this->files = collect($this->request->getUploadedFiles())
			->map($this->saveFileToFilesystem(...))
			->all();
		
		$this->request = $this->request->withUploadedFiles([]);
	}
	
	protected function saveFileToFilesystem(UploadedFileInterface $file): array
	{
		if ($file->getError()) {
			return [
				'path' => '',
				'original_filename' => $file->getClientFilename(),
				'mime' => $file->getClientMediaType(),
				'error' => $file->getError(),
			];
		}
		
		$stream = $file->getStream();
		$destination = tempnam(sys_get_temp_dir(), 'dawn');
		
		if ($stream->isSeekable()) {
			$stream->rewind();
		}
		
		$handle = fopen($destination, 'w');
		
		while (! $stream->eof()) {
			fwrite($handle, $stream->read(1048576));
		}
		
		fclose($handle);
		
		return [
			'path' => $destination,
			'original_filename' => $file->getClientFilename(),
			'mime' => $file->getClientMediaType(),
			'error' => UPLOAD_ERR_OK,
		];
	}
	
	protected function runRequestThroughKernel(Request $request): Response
	{
		$kernel = app(HttpKernel::class);
		
		$response = $kernel->handle($request);
		
		$kernel->terminate($request, $response);
		
		return $response;
	}
}

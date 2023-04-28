<?php

namespace Glhd\Dawn\Support;

use Illuminate\Support\Arr;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class CachedBodyStream implements StreamInterface
{
	/** @var resource */
	protected $stream = null;
	
	protected bool $detached = false;
	
	public function __construct(
		public string $filename
	) {
	}
	
	public function __toString()
	{
		$this->seek(0);
		
		return $this->getContents();
	}
	
	public function close(): void
	{
		if (is_resource($this->stream)) {
			fclose($this->stream);
		}
		
		$this->detach();
	}
	
	public function detach()
	{
		$result = $this->stream;
		
		$this->stream = null;
		$this->detached = true;
		
		return $result;
	}
	
	public function getSize(): ?int
	{
		if (null === $this->stream()) {
			return null;
		}
		
		// Clear the stat cache if the stream has a URI
		if ($uri = $this->getMetadata('uri')) {
			clearstatcache(true, $uri);
		}
		
		return Arr::get(fstat($this->stream()), 'size');
	}
	
	public function tell(): int
	{
		$result = @ftell($this->streamOrFail());
			
		if (false === $result) {
			throw new RuntimeException('Unable to determine stream position: '.(error_get_last()['message'] ?? ''));
		}
		
		return $result;
	}
	
	public function eof(): bool
	{
		return null === $this->stream() || feof($this->stream());
	}
	
	public function isSeekable(): bool
	{
		return true;
	}
	
	public function seek($offset, $whence = \SEEK_SET): void
	{
		if (-1 === fseek($this->streamOrFail(), $offset, $whence)) {
			throw new RuntimeException("Unable to seek to stream position '{$offset}'.");
		}
	}
	
	public function rewind(): void
	{
		$this->seek(0);
	}
	
	public function isWritable(): bool
	{
		return false;
	}
	
	public function write($string): int
	{
		throw new RuntimeException('Cannot write to a cached body stream');
	}
	
	public function isReadable(): bool
	{
		return true;
	}
	
	public function read($length): string
	{
		$result = @fread($this->streamOrFail(), $length);
		
		if (false === $result) {
			throw new RuntimeException('Unable to read from stream: '.(error_get_last()['message'] ?? ''));
		}
		
		return $result;
	}
	
	public function getContents(): string
	{
		$contents = @stream_get_contents($this->streamOrFail());
		
		if (false === $contents) {
			throw new RuntimeException('Unable to read stream contents: '.(error_get_last()['message'] ?? ''));
		}
		
		return $contents;
	}
	
	public function getMetadata($key = null)
	{
		return Arr::get(stream_get_meta_data($this->stream()), $key);
	}
	
	public function __serialize(): array
	{
		return ['filename' => $this->filename];
	}
	
	public function __unserialize(array $data): void
	{
		$this->filename = $data['filename'];
		$this->detached = false;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	/** @return null|resource */
	protected function stream()
	{
		if ($this->detached) {
			return null;
		}
		
		return $this->stream ??= fopen($this->filename, 'r');
	}
	
	/** @return resource */
	protected function streamOrFail()
	{
		if (null === $this->stream()) {
			throw new RuntimeException('Stream is detached');
		}
		
		return $this->stream();
	}
}

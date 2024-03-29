<?php

namespace Glhd\Dawn\Browser\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;

trait NormalizesStoragePaths
{
	protected function prepareAndNormalizeStoragePath(string $filename, string $directory): string
	{
		$fs = new Filesystem();
		
		if (! $this->isPathAbsolute($filename)) {
			$filename = rtrim($directory, '/').'/'.ltrim($filename, '/');
		}
		
		$fs->ensureDirectoryExists($fs->dirname($filename));
		
		return $filename;
	}
	
	protected function isPathAbsolute(string $path): bool
	{
		return (bool) preg_match('#([a-z]:)?[/\\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
	}
}

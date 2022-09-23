<?php

namespace Glhd\Dawn\Support;

use Illuminate\Support\Facades\App;

class ArtisanExecutableFinder
{
	public function __construct(
		protected ?string $cwd = null,
		protected array $names = ['artisan'],
	) {
		$this->cwd ??= base_path();
	}
	
	public function find(?string $in = null): ?string
	{
		$in ??= $this->cwd;
		
		if (file_exists("{$in}/composer.json")) {
			if (file_exists($path = "{$in}/artisan")) {
				return $path;
			}
			if (App::runningUnitTests() && file_exists($path = "{$in}/testbench")) {
				return $path;
			}
		}
		
		$parent = dirname($in);
		if ($parent !== $in) {
			return $this->find($parent);
		}
		
		return null;
	}
}

<?php

namespace Glhd\Dawn\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GenerateCommandHelpersCommand extends Command
{
	protected $signature = 'dawn:generate-command-helpers';
	
	protected $hidden = true;
	
	protected string $base;
	
	public function handle()
	{
		$this->base = rtrim(realpath(__DIR__.'/../../Browser/Commands'), '/').'/';
		
		$this->line('Generating traits...');
		$this->newLine();
		
		$traits = collect(Finder::create()->files()->in($this->base)->name('*.php'))
			->reduce(function(Collection $traits, SplFileInfo $file) {
				[$trait, $imports, $function_name, $function_body] = $this->handleFile($file);
				
				if (! $trait) {
					return $traits;
				}
				
				$traits[$trait] ??= (object) [
					'trait' => $trait,
					'imports' => new Collection(),
					'functions' => new Collection(),
				];
				
				$traits[$trait]->imports->push(...$imports->all());
				$traits[$trait]->functions->put($function_name, $function_body);
				
				return $traits;
			}, new Collection())
			->map(function($trait) {
				$functions = $trait->functions->sortKeys()->values()->implode("\n\t\n");
				
				$imports = $trait->imports->unique()
					->filter(function($import) use ($functions) {
						return Str::contains($functions, [$import, "\{$import}"])
							|| preg_match('/(?:^|[^a-z])'.preg_quote(class_basename($import), '/').'[: (|]/', $functions);
					})
					->sort()
					->map(fn($import) => "use {$import};")
					->implode("\n");
				
				$code = $this->template($trait->trait, $imports, $functions);
				
				$fs = new Filesystem();
				$path = __DIR__.'/../../Browser/Concerns/'.$trait->trait.'.php';
				$fs->put($path, $code);
				
				return "use {$trait->trait};";
			})
			->sort()
			->each(fn($use) => $this->line($use))
			->implode("\n\t");
		
		$fs = new Filesystem();
		$path = __DIR__.'/../../Browser/Concerns/ExecutesCommands.php';
		$code = <<<PHP
		<?php
		
		namespace Glhd\Dawn\Browser\Concerns;
		
		/**
		 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
		 *
		 * @see \Glhd\Dawn\Console\Commands\GenerateCommandHelpersCommand
		 */
		trait ExecutesCommands
		{
			{$traits}
		}
		
		PHP;
		
		$fs->put($path, $code);
		
		$this->newLine();
	}
	
	protected function handleFile(SplFileInfo $file): array
	{
		$source = $file->getContents();
		
		$trait = Str::of($file->getRelativePath())
			->whenEmpty(fn() => Str::of('Browser'))
			->replace('//', '')
			->singular()
			->prepend('Executes')
			->append('Commands');
		
		$classname = $file->getBasename('.php');
		$fqcn = $this->getNamespace($source).'\\'.$classname;
		$function_name = lcfirst($classname);
		
		if (! Str::contains($source, ['extends BrowserCommand', 'extends BrowserAssertionCommand'])) {
			return [null, null, null, null];
		}
		
		$imports = $this->getImports($source);
		$imports->push($fqcn);
		
		$return_type = $this->getReturnType($source);
		
		$parameters = $this->getParameters($source);
		$function = <<<END_CODE
			public function {$function_name}({$parameters->arguments}): {$return_type}
			{
				return \$this->command(new {$classname}({$parameters->calls}));
			}
		END_CODE;
		
		// Allow "mixed" returns to be fluent
		if ('mixed' === $return_type) {
			$function = "\t/** @return \$this|mixed */\n$function";
		}
		
		return [(string) $trait, $imports, $function_name, $function];
	}
	
	protected function getReturnType(string $source): string
	{
		$return_type_pattern = '/function executeWithBrowser\([^)]*\):\s+(?P<return_type>.+)\s*$/m';
		
		if (preg_match($return_type_pattern, $source, $matches)) {
			return trim($matches['return_type']);
		}
		
		return Str::contains($source, 'implements ValueCommand')
			? 'mixed'
			: 'static';
	}
	
	protected function getParameters(string $source): stdClass
	{
		if (! preg_match('/__construct\((.*?)\)/s', $source, $matches)) {
			return (object) [
				'arguments' => '',
				'calls' => '',
			];
		}
		
		[$arguments, $calls] = Str::of($matches[1])
			->explode(',')
			->map(fn($parameter) => trim($parameter))
			->filter()
			->map(function($parameter) {
				$pattern = '/^\s*(?:private|protected|public)?(?:\s+readonly)?\s*(?:(?P<type>[^\s]*)\s+)(?P<variable>\$[a-z0-9_]+)(?:\s*(?P<defaults>=.*)\s*)?$/i';
				preg_match($pattern, $parameter, $matches);
				return (object) $matches;
			})
			->reduceSpread(function(Collection $arguments, Collection $calls, stdClass $parameter) {
				$arguments->push(collect([$parameter->type ?? null, $parameter->variable, $parameter->defaults ?? null])->filter()->implode(' '));
				$calls->push($parameter->variable);
				return [$arguments, $calls];
			}, new Collection(), new Collection());
		
		return (object) [
			'arguments' => $arguments->implode(', '),
			'calls' => $calls->implode(', '),
		];
	}
	
	protected function getImports(string $source): Collection
	{
		preg_match_all('/use\s+(.*);/i', $source, $matches);
		
		return collect($matches[1] ?? []);
	}
	
	protected function getNamespace(string $source): string
	{
		preg_match('/^namespace (Glhd\\\\Dawn.*);/im', $source, $matches);
		
		return $matches[1];
	}
	
	protected function template(string $trait, string $imports, string $functions): string
	{
		$fqcn = static::class;
		
		return <<<PHP
		<?php
		
		namespace Glhd\Dawn\Browser\Concerns;

		{$imports}
		
		/**
		 * This file is auto-generated using `php artisan dawn:generate-command-helpers`
		 *
		 * @see \\{$fqcn}
		 */
		trait {$trait}
		{
		{$functions}
		}
		
		PHP;
	}
}

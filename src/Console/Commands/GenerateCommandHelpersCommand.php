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
		
		collect(Finder::create()->files()->in($this->base)->name('*.php'))
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
					->filter(fn($import) => Str::contains($functions, [
						$import,
						"\{$import}",
						class_basename($import),
					]))
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
			->each(fn($use) => $this->line($use));
		
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
		
		$parameters = $this->getParameters($source);
		$function = <<<END_CODE
			public function {$function_name}({$parameters->arguments}): static
			{
				return \$this->command(new {$classname}({$parameters->calls}));
			}
		END_CODE;
		
		return [(string) $trait, $imports, $function_name, $function];
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

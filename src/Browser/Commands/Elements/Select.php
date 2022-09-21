<?php

namespace Glhd\Dawn\Browser\Commands\Elements;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Browser\Commands\BrowserCommand;
use Glhd\Dawn\Browser\Commands\Concerns\UsesSelectors;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends BrowserCommand
{
	use UsesSelectors;
	
	public function __construct(
		public WebDriverBy|string $selector,
		public string|array|null $value = null,
	) {
		if (null !== $this->value) {
			$this->value = $this->normalizeValue($this->value);
		}
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$element = $manager->resolver->resolveForSelection($this->selector);
		
		if ($multiple = $this->isMultiple($element)) {
			$this->deselectAll($element);
		}
		
		$this->clickMatchingOptions($this->getEnabledOptions($element), $multiple);
	}
	
	protected function clickMatchingOptions(Collection $options, bool $multiple = false)
	{
		if (null === $this->value) {
			$options->random()->click();
			return;
		}
		
		foreach ($options as $option) {
			if (in_array((string) $option->getAttribute('value'), $this->value)) {
				$option->click();
				
				if (! $multiple) {
					return;
				}
			}
		}
	}
	
	protected function normalizeValue($value): array
	{
		return collect(Arr::wrap($value))
			->map(fn($value) => match ($value) {
				true => '1',
				false => '0',
				default => (string) $value,
			})
			->all();
	}
	
	protected function isMultiple(RemoteWebElement $element): bool
	{
		if ('select' !== $element->getTagName()) {
			return false;
		}
		
		return (new WebDriverSelect($element))->isMultiple();
	}
	
	protected function deselectAll(RemoteWebElement $element): void
	{
		(new WebDriverSelect($element))->deselectAll();
	}
	
	protected function getEnabledOptions(RemoteWebElement $element): Collection
	{
		$selector = WebDriverBy::cssSelector('option:not([disabled])');
		
		return collect($element->findElements($selector));
	}
}

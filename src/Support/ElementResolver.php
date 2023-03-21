<?php

namespace Glhd\Dawn\Support;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use Throwable;

class ElementResolver
{
	public function __construct(
		protected RemoteWebDriver $driver,
		protected string $prefix = 'body',
		protected $elements = [],
	) {
	}
	
	public function inParent(WebDriverBy|string $selector): static
	{
		$resolver = clone $this;
		$resolver->prefix = $this->toCssSelector($selector);
		
		return $resolver;
	}
	
	public function pageElements(array $elements): static
	{
		$this->elements = $elements;
		
		return $this;
	}
	
	public function resolveForTyping(WebDriverBy|string $field): RemoteWebElement
	{
		$field = $this->toCssSelector($field);
		
		return $this->findByIdOrSelectors($field, [
			"input[name='{$this->escapeSelector($field)}']",
			"textarea[name='{$this->escapeSelector($field)}']",
		]);
	}
	
	public function resolveForSelection(WebDriverBy|string $field): RemoteWebElement
	{
		$field = $this->toCssSelector($field);
		
		return $this->findByIdOrSelectors($field, [
			"select[name='{$this->escapeSelector($field)}']",
		]);
	}
	
	/** @return \Facebook\WebDriver\Remote\RemoteWebElement[] */
	public function resolveSelectOptions(WebDriverBy|string $field, array $values): array
	{
		$field = $this->toCssSelector($field);
		
		$options = $this->resolveForSelection($field)
			->findElements(WebDriverBy::tagName('option'));
		
		return collect($options)
			->filter(function(RemoteWebElement $element) use ($values) {
				return in_array($element->getAttribute('value'), $values);
			})
			->all();
	}
	
	public function resolveForRadioSelection(WebDriverBy|string $field, string $value = null): RemoteWebElement
	{
		$field = $this->toCssSelector($field);
		
		if ($element = $this->findById($field)) {
			return $element;
		}
		
		if (null === $value) {
			throw new InvalidArgumentException("No value was provided for radio button [{$field}].");
		}
		
		return $this->firstOrFail([
			"input[type=radio][name='{$this->escapeSelector($field)}'][value='{$this->escapeSelector($value)}']",
			$field,
		]);
	}
	
	public function resolveForChecking(WebDriverBy|string|null $field = null, string $value = null): RemoteWebElement
	{
		$field = $this->toOptionalCssSelector($field);
		
		return $this->findByIdOrSelectors($field, [
			(string) Str::of('input[type=checkbox]')
				->when($field, fn(Stringable $selector) => $selector->append("[name='{$this->escapeSelector($field)}']"))
				->when($value, fn(Stringable $selector) => $selector->append("[value='{$this->escapeSelector($value)}']")),
		]);
	}
	
	public function resolveForAttachment(WebDriverBy|string $field): RemoteWebElement
	{
		$field = $this->toCssSelector($field);
		
		return $this->findByIdOrSelectors($field, [
			"input[type=file][name='{$this->escapeSelector($field)}']",
		]);
	}
	
	public function resolveForField(WebDriverBy|string $field): RemoteWebElement
	{
		$field = $this->toCssSelector($field);
		
		return $this->findByIdOrSelectors($field, [
			"input[name='{$this->escapeSelector($field)}']",
			"textarea[name='{$this->escapeSelector($field)}']",
			"select[name='{$this->escapeSelector($field)}']",
			"button[name='{$this->escapeSelector($field)}']",
		]);
	}
	
	public function resolveForButtonPress(WebDriverBy|string $button): RemoteWebElement
	{
		$button = $this->toCssSelector($button);
		
		$strategies = [
			$this->findById(...),
			$this->find(...),
			$this->findButtonByName(...),
			$this->findButtonByValue(...),
			$this->findButtonByText(...),
		];
		
		foreach ($strategies as $strategy) {
			if ($element = $strategy($button)) {
				return $element;
			}
		}
		
		throw new InvalidArgumentException("Unable to locate button [{$button}].");
	}
	
	public function find(string|WebDriverBy $selector): ?RemoteWebElement
	{
		try {
			return $this->findOrFail($selector);
		} catch (Throwable) {
			return null;
		}
	}
	
	public function findByIdOrSelectors(string $id, array $selectors): RemoteWebElement
	{
		$selectors[] = $id;
		
		return $this->findById($id) ?? $this->firstOrFail($selectors);
	}
	
	public function firstOrFail(string|WebDriverBy|array $selectors): RemoteWebElement
	{
		foreach ((array) $selectors as $selector) {
			try {
				return $this->findOrFail($selector);
			} catch (Exception $e) {
				// We'll throw this later if we don't find anything with another selector
			}
		}
		
		throw $e;
	}
	
	public function first(string|WebDriverBy|array $selectors): ?RemoteWebElement
	{
		try {
			return $this->firstOrFail($selectors);
		} catch (Exception) {
			return null;
		}
	}
	
	public function findOrFail(string|WebDriverBy $selector): RemoteWebElement
	{
		$css_mechanisms = ['class name', 'id', 'name', 'tag name'];
		
		if ($selector instanceof WebDriverBy && ! in_array($selector->getMechanism(), $css_mechanisms)) {
			return $this->driver->findElement($selector);
		}
		
		$selector = $this->toCssSelector($selector);
		
		return $this->findById($selector) ?? $this->findBySelector($selector);
	}
	
	public function findBySelector(string $selector): RemoteWebElement
	{
		return $this->findBy(WebDriverBy::cssSelector($this->format($selector)));
	}
	
	public function findBy(WebDriverBy $by): RemoteWebElement
	{
		return $this->driver->findElement($by);
	}
	
	/** @return \Facebook\WebDriver\Remote\RemoteWebElement[] */
	public function all(string $selector): array
	{
		try {
			return $this->driver->findElements(WebDriverBy::cssSelector($this->format($selector)));
		} catch (Throwable) {
			return [];
		}
	}
	
	public function format(WebDriverBy|string $selector): string
	{
		$selector = $this->toCssSelector($selector);
		
		$sorted_elements = collect($this->elements)
			->sortByDesc(fn($element, $key) => strlen($key))
			->all();
		
		$mapped_selector = str_replace(
			search: array_keys($sorted_elements),
			replace: array_values($sorted_elements),
			subject: $selector,
		);
		
		if (Str::startsWith($selector, '@') && $mapped_selector === $selector) {
			$attribute = config('dawn.target_attribute', 'data-dawn-target');
			$selector = '['.$attribute.'="'.$this->escapeSelector(Str::after($selector, '@')).'"]';
		}
		
		return trim($this->prefix.' '.$selector);
	}
	
	protected function toCssSelector(WebDriverBy|string $field): string
	{
		if ($field instanceof WebDriverBy) {
			$field = match ($field->getMechanism()) {
				'class name' => ".{$this->escapeSelector($field)}",
				'id' => "#{$this->escapeSelector($field)}",
				'name' => "[name='{$this->escapeSelector($field)}']",
				default => $field->getValue(),
			};
		}
		
		return $field;
	}
	
	protected function toOptionalCssSelector(WebDriverBy|string|null $field): ?string
	{
		if (null === $field) {
			return null;
		}
		
		return $this->toCssSelector($field);
	}
	
	protected function escapeSelector(string|WebDriverBy $selector): string
	{
		if ($selector instanceof WebDriverBy) {
			$selector = $selector->getValue();
		}
		
		return preg_replace_callback('/[^a-z0-9]/iSu', function($matches) {
			$chr = $matches[0];
			if (mb_strlen($chr) === 1) {
				$ord = ord($chr);
			} else {
				$chr = mb_convert_encoding($chr, 'UTF-32BE', 'UTF-8');
				$ord = hexdec(bin2hex($chr));
			}
			
			return sprintf('\\%X ', $ord);
		}, $selector);
	}
	
	protected function findButtonByName(string $button): ?RemoteWebElement
	{
		return $this->find("input[type=submit][name='{$button}']")
			?? $this->find("input[type=button][value='{$button}']")
			?? $this->find("button[name='{$button}']");
	}
	
	protected function findButtonByValue(string $value): ?RemoteWebElement
	{
		return collect($this->all('input[type=submit]'))
			->first(fn(RemoteWebElement $element) => $value === $element->getAttribute('value'));
	}
	
	protected function findButtonByText(string $text): ?RemoteWebElement
	{
		return collect($this->all('button'))
			->first(fn(RemoteWebElement $element) => Str::contains($element->getText(), $text));
	}
	
	protected function findById(string $selector): ?RemoteWebElement
	{
		return preg_match('/^#[\w\-:]+$/', $selector)
			? $this->driver->findElement(WebDriverBy::id(substr($selector, 1)))
			: null;
	}
}

<?php

namespace Glhd\Dawn\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

trait HasBrowserAssertionAliases
{
	public function assertCookieValue(string $name, $value, bool $decrypt = true): static
	{
		return $this->assertHasCookie($name, $value, $decrypt);
	}
	
	public function assertPlainCookieValue(string $name, $value): static
	{
		return $this->assertHasCookie($name, $value, decrypt: false);
	}
	
	public function assertHasPlainCookie(string $name): static
	{
		return $this->assertHasCookie($name, decrypt: false);
	}
	
	public function assertPlainCookieMissing(string $name): static
	{
		return $this->assertCookieMissing($name, decrypt: false);
	}
	
	public function assertSee($text): static
	{
		return $this->assertSeeIn('', $text);
	}
	
	public function assertDontSee($text): static
	{
		return $this->assertDontSeeIn('', $text);
	}
	
	public function assertSeeLink($link): static
	{
		return $this->assertLinkVisibility($link, expected: true);
	}
	
	public function assertDontSeeLink($link): static
	{
		return $this->assertLinkVisibility($link, expected: false);
	}
	
	public function assertInputValueIsNot(WebDriverBy|string $selector, $value): static
	{
		return $this->assertInputValue($selector, $value, not: true);
	}
	
	public function assertPresent(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_exists: true);
	}
	
	public function assertNotPresent(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_exists: false);
	}
	
	public function assertVisible(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_displayed: true);
	}
	
	public function assertMissing(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_displayed: false);
	}
	
	public function assertEnabled(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_enabled: true);
	}
	
	public function assertDisabled(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_enabled: false);
	}
	
	public function assertFocused(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_focused: true);
	}
	
	public function assertNotFocused(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, expect_focused: false);
	}
	
	public function assertButtonEnabled(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, resolver: 'resolveForButtonPress', expect_enabled: true);
	}
	
	public function assertButtonDisabled(WebDriverBy|string $selector): static
	{
		return $this->assertElementStatus($selector, resolver: 'resolveForButtonPress', expect_enabled: false);
	}
	
	public function assertInputPresent(string $name): static
	{
		return $this->assertElementStatus(
			selector: "input[name='{$name}'], textarea[name='{$name}'], select[name='{$name}']"
		);
	}
	
	public function assertInputMissing(string $name): static
	{
		return $this->assertElementStatus(
			selector:"input[name='{$name}'], textarea[name='{$name}'], select[name='{$name}']",
			expect_exists: false
		);
	}
	
	public function assertChecked(WebDriverBy|string $selector, $value = null): static
	{
		return $this->assertSelectionState(
			selector: $selector,
			value: $value,
			resolver: 'resolveForChecking',
			message: 'Expected checkbox [%s] to be checked, but it wasn\'t.'
		);
	}
	
	public function assertNotChecked(WebDriverBy|string $selector, $value = null): static
	{
		return $this->assertSelectionState(
			selector: $selector,
			value: $value,
			expected: false,
			resolver: 'resolveForChecking',
			message: 'Checkbox [%s] was unexpectedly checked.'
		);
	}
	
	public function assertIndeterminate(WebDriverBy|string $selector, $value = null): static
	{
		return $this->assertSelectionState(
			selector: $selector,
			value: $value,
			expected: false,
			expect_indeterminate: true,
			resolver: 'resolveForChecking',
			message: 'Checkbox [%s] was not in indeterminate state.'
		);
	}
	
	public function assertRadioSelected(WebDriverBy|string $selector, $value = null): static
	{
		return $this->assertSelectionState(
			selector: $selector,
			value: $value,
			resolver: 'resolveForRadioSelection',
			message: 'Expected radio [%s] to be selected, but it wasn\'t.'
		);
	}
	
	public function assertRadioNotSelected(WebDriverBy|string $selector, $value = null): static
	{
		return $this->assertSelectionState(
			selector: $selector,
			value: $value,
			expected: false,
			resolver: 'resolveForRadioSelection',
			message: 'Radio [%s] was unexpectedly selected.'
		);
	}
	
	public function assertSelected(WebDriverBy|string $selector, $value): static
	{
		return $this->assertOptionSelectionState(
			selector: $selector, 
			value: $value,
			message: 'Expected value [%s] to be selected for [%s], but it wasn\'t.'
		);
	}
	
	public function assertNotSelected(WebDriverBy|string $selector, $value): static
	{
		return $this->assertOptionSelectionState(
			selector: $selector,
			value: $value,
			expected: false,
			message: 'Unexpected value [%s] selected for [%s].'
		);
	}
	
	public function assertSelectHasOptions(WebDriverBy|string $selector, array $options): static
	{
		return $this->assertOptionPresence(
			selector: $selector, 
			options: $options,
			message: 'Expected options [%s] for selection field [%s] to be available.',
		);
	}
	
	public function assertSelectHasOption(WebDriverBy|string $selector, $option): static
	{
		return $this->assertSelectHasOptions($selector, [$option]);
	}
	
	public function assertSelectMissingOptions(WebDriverBy|string $selector, array $options): static
	{
		return $this->assertOptionPresence(
			selector: $selector,
			options: $options,
			expected: false,
			message: 'Unexpected options [%s] for selection field [%s].',
		);
	}
	
	public function assertSelectMissingOption(WebDriverBy|string $selector, $option): static
	{
		return $this->assertSelectMissingOptions($selector, [$option]);
	}
	
	public function assertValueIsNot(WebDriverBy|string $selector, $value): static
	{
		return $this->assertValue($selector, $value, not: true);
	}
	
	public function assertAriaAttribute(WebDriverBy|string $selector, string $attribute, $value): static
	{
		return $this->assertAttribute($selector, 'aria-'.$attribute, $value);
	}
	
	public function assertDataAttribute(WebDriverBy|string $selector, $attribute, $value): static
	{
		return $this->assertAttribute($selector, 'data-'.$attribute, $value);
	}
}

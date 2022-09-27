<div style="float: right;">
	<a href="https://github.com/glhd/dawn/actions" target="_blank">
		<img 
			src="https://github.com/glhd/dawn/workflows/PHPUnit/badge.svg" 
			alt="Build Status" 
		/>
	</a>
    <a href="https://codeclimate.com/github/glhd/dawn/test_coverage" target="_blank">
        <img 
            src="https://api.codeclimate.com/v1/badges/a7c4b59f7195ed254ab7/test_coverage"
            alt="Test Coverage Status"
        />
    </a>
	<a href="https://packagist.org/packages/glhd/dawn" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/dawn/v/stable" 
            alt="Latest Stable Release" 
        />
	</a>
	<a href="./LICENSE" target="_blank">
        <img 
            src="https://poser.pugx.org/glhd/dawn/license" 
            alt="MIT Licensed" 
        />
    </a>
    <a href="https://twitter.com/inxilpro" target="_blank">
        <img 
            src="https://img.shields.io/twitter/follow/inxilpro?style=social" 
            alt="Follow @inxilpro on Twitter" 
        />
    </a>
</div>

# Dawn

Dawn is an experimental browser-testing library for Laravel. It aims to be mostly compatible with Dusk, 
but with different trade-offs[^1]. The main benefit of Dawn is that it allows you to write browser tests
exactly as you write all other feature tests (database transactions, mocks, custom testing routes, etc).
This generally means that they run faster and with fewer restrictions.

> **Warning**
> This is a very early release. Some edge-cases have been accounted for. Many have not. Much of the Dusk 
> API has been implemented, but plenty of methods and assertions are missing.

## Installation

### Install Dawn
You can install the development release of Dawn via Composer (you'll need PHP 8.1 and Laravel 9):

```shell
# composer require glhd/dawn:dev-main
```

### Install Chrome Driver globally
You'll also need [chromedriver](https://chromedriver.chromium.org/downloads) installed on your machine.

> **Note**
> Eventually, Dawn will install a copy of chromedriver for you, just like Dusk. The current
> implementation has only been tested on MacOS with chromedriver installed via homebrew. YMMV.

## Usage

To use Dawn, add `RunsBrowserTests` to any test case. Then you can call `openBrowser()` to get
a `Browser` instance to start testing with.

```php
class MyBrowserTest extends TestCase
{
  use RefreshDatabase;
  use RunsBrowserTests;
  
  public function test_can_visit_homepage() 
  {
    $this->openBrowser() // <-- this is Dawn
      ->visit('/')
      ->assertTitleContains('Home');
  }
}
```

### Dawn API

Dawn aims to have an API that is mostly compatible with [Laravel Dusk](https://laravel.com/docs/9.x/dusk).
Not all features or assertions are implemented, but for right now you're best using the Dusk documentation
for reference.

### Dusk API Compatibility

Much of the Dusk API has been implemented, but not all of it.

#### Missing methods (may not be exhaustive):

- `whenAvailable()`
- `waitUntilEnabled()`
- `waitUntilDisabled()`
- `waitUntilVue()`
- `waitUntilVueIsNot()`
- `waitForReload()`
- `clickAndWaitForReload()`
- `waitForEvent()`
- `attach()`
- `pressAndWaitFor()`
- `drag()`
- `dragUp()`
- `dragDown()`
- `dragLeft()`
- `dragRight()`
- `dragOffset()`
- `moveMouse()`
- `mouseover()`
- `clickAtPoint()`
- `clickAtXPath()`
- `clickAndHold()`
- `doubleClick()`
- `rightClick()`
- `releaseMouse()`

#### Missing assertions (may not be exhaustive):

- `assertVueContains()`
- `assertVueDoesNotContain()`
- `assertQueryStringHas()`
- `assertQueryStringMissing()`

## FAQ

<dl>
<dt>Does it work on anything other that Chris's Mac?</dt>
<dd>🤷‍♂️</dd>
<dt>Will it ever work on Windows?</dt>
<dd>🤷‍♂️ Probably?</dd>
<dt>When can I use this?</dt>
<dd>Fortune favors the brave.</dd>
<dt>Is this the final API?</dt>
<dd>Most of the `RunsBrowserTests` is pretty solid. The underlying implementation may change a ton before 1.0.</dd>
</dl>

## Major TODO Items

- Finish copying over the Dusk API
- Full test coverage
- Get automated tests running on Windows
- Improve the `chromedriver` installation/etc story

[^1]: Dawn is generally easier to use out of the box, but writing custom low-level WebDriver code
      is trickier due to how Dawn works under the hood.

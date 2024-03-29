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
composer require glhd/dawn:dev-main
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

#### Differences from Dusk

The primary API difference between Dawn and Dusk is that the Dawn APIs do not require browser
interactions to happen inside of callbacks. For the most part, this just involves replacing
calls to `$this->browse()` with `$this->openBrowser()` and removing the closure:

```diff
-$this->browse(function ($browser) {
+$browser = $this->openBrowser();
$browser->visit('/login')
-});
```

There are certain Dusk methods are either tricky to implement with Dawn's async I/O channel, or are
impossible to recreate exactly due to serialization constraints (you cannot serialize a TCP connection, for example).
Here are some such functions:

- `login()`, `loginAs()`, etc — these don't make any sense in Dawn, because you can just
  use the normal `actingAs()` or `be()` helper methods in your test.
- `cookie()` and `plainCookie()` — these will eventually be implemented
- `element()` and `elements()` — because Dawn interacts with the WebDriver instance in a background process,
  it is a little harder to get direct access to the underlying `RemoteWebElement` instances in your main
  PHPUnit process. There will eventually be an API for accessing these, but it will likely work slightly differently.
- `ensurejQueryIsAvailable()` — Dawn does not rely on jQuery

### Dusk API Compatibility

Much of the Dusk API has been implemented, but not all of it.

#### Missing methods (may not be exhaustive):

- `pressAndWaitFor()`
- `within()`/`with()`/`elsewhere()`/`elsewhereWhenAvailable()` (scopes are generally not implemented yet)
- `onComponent()` (components aren't implemented yet)

#### Missing assertions (may not be exhaustive):

- `assertVueContains()`
- `assertVueDoesNotContain()`
- `assertQueryStringHas()`
- `assertQueryStringMissing()`

## Troubleshooting

<dl>
<dt>I get an error like `Failed to connect to localhost port 9515 after 2 ms: Couldn't connect to server`</dt>
<dd>Make sure you have both `chromedriver` and the same version of Google Chrome installed.</dd>
</dl>

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
- Get drag and drop working

[^1]: Dawn is generally easier to use out of the box, but writing custom low-level WebDriver code
      is trickier due to how Dawn works under the hood.

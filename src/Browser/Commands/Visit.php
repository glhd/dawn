<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\Browser\BrowserManager;
use Glhd\Dawn\Http\WebServerBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Visit extends BrowserCommand
{
	public string $url;
	
	public function __construct(string $url)
	{
		$this->url = match (true) {
			Str::is('about:*', $url) => $url,
			default => url($url),
		};
		
		$this->rewriteUrlIfHandledByApplication();
	}
	
	protected function rewriteUrlIfHandledByApplication()
	{
		try {
			Route::getRoutes()->match(Request::create($this->url));
		} catch (NotFoundHttpException) {
			return;
		}
		
		// FIXME: Maybe include the original URL as the user parameter?
		
		$parts = array_merge(
			parse_url($this->url),
			parse_url(app(WebServerBroker::class)->url()),
		);
		
		$this->url = $this->rebuildUrl($parts);
	}
	
	protected function rebuildUrl(array $parts): string
	{
		$url = '';
		$keys = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'];
		
		foreach ($keys as $key) {
			if (! $value = Arr::get($parts, $key)) {
				continue;
			}
			
			$url .= match ($key) {
				'scheme' => "{$value}://",
				'user' => $value,
				'pass' => ":{$value}",
				'host' => isset($parts['user'])
					? "@{$value}"
					: $value,
				'port' => 80 === (int) $value
					? ''
					: ":{$value}",
				'path' => $value,
				'query' => "?{$value}",
				'fragment' => "#{$value}",
			};
		}
		
		return $url;
	}
	
	protected function executeWithBrowser(BrowserManager $manager)
	{
		$manager->get($this->url);
	}
}

<?php namespace Waavi\Translation;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Middleware implements \Illuminate\Contracts\Routing\Middleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next)
	{
		if($request->has('trans')) \Config::set('translation.debug', true);

		$response = $next($request);
		// Check PHP extension
		if( !config('translation.debug'))
			return $response;
		// Skip special response types
		if(($response instanceof BinaryFileResponse) ||
		($response instanceof JsonResponse) ||
		($response instanceof RedirectResponse) ||
		($response instanceof StreamedResponse))
			return $response;

		// Convert unknown responses
		if( ! $response instanceof Response)
		{
			$response = new Response($response);
			if( ! $response->headers->has('content-type'))
				$response->headers->set('content-type', 'text/html');
		}
		// If response is HTML parse it
		$contentType = $response->headers->get('content-type');
		if(str_contains($contentType, 'text/html')){
			$content = $response->getContent();
			$dom = new \DOMDocument;
			@$dom->loadHTML($content);
			$head = $dom->getElementsByTagName('head');
			if(count($head) > 0){
				$script = $dom->createElement("script");
				$script->setAttribute('type', 'text/javascript');
				$scriptContents = file_get_contents(__DIR__.'/translation.js');
				$script->appendChild($dom->createTextNode('var lang = '.app('translator')->getAllHashed().';'));
				$script->appendChild($dom->createTextNode($scriptContents));
				$head->item(0)->appendChild($script);
			}
			$response->setContent(preg_replace("/\{\{trans:([a-f0-9]{32})\}\}/i", '<span class="trans-$1"></span>', $dom->saveHTML()));
		}
		return $response;
	}
}
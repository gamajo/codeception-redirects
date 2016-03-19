<?php

namespace Codeception\Module;

use Codeception\Module;

class Redirects extends Module{

    /**
     * Protocol constants that can be used with permanentRedirectForProtocol() method.
     */
    const PROTOCOL_HTTP = 'http';
    const PROTOCOL_HTTPS = 'https';

	/**
	 * Check that a 301 HTTP Status is returned with the correct Location URL.
	 *
	 * @since 0.1.0
	 *
	 * @param string $url Relative or absolute URL of redirect destination.
	 */
	public function seePermanentRedirectTo($url)
	{
		$response       = $this->getModule('PhpBrowser')->client->getInternalResponse();
		$responseCode   = $response->getStatus();
		$locationHeader = $response->getHeader('Location', true);

		// Check for 301 response code.
		$this->assertEquals(301, $responseCode);

		// Check location header URL contains submitted URL.
		$this->assertContains($url, $locationHeader);
	}

	/**
	 * Check that a 200 HTTP Status is returned with the correct Location URL.
	 *
	 * Should be HTTPS.
	 *
	 * @since 0.1.0
	 *
	 * @param string $url Relative or absolute URL of redirect destination.
	 */
	public function seePermanentRedirectToHttpsFor($url)
	{
		$this->permanentRedirectForProtocol($url, self::PROTOCOL_HTTPS);
	}

	/**
	 * Check that a 200 HTTP Status is returned with the correct Location URL.
	 *
	 * Should be HTTP.
	 *
	 * @since 0.1.0
	 *
	 * @param string $url Relative or absolute URL of redirect destination.
	 */
	public function seePermanentRedirectToHttpFor($url)
	{
		$this->permanentRedirectForProtocol($url, self::PROTOCOL_HTTP);
	}

	/**
	 * Toggle redirections on and off.
	 *
	 * By default, BrowserKit will follow redirections, so to check for 30*
	 * HTTP status codes and Location headers, they have to be turned off.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $followRedirects Optional. Whether to follow redirects or not.
	 *                              Default is true.
	 */
	public function followRedirects($followRedirects = true)
	{
		$this->getModule('PhpBrowser')->client->followRedirects($followRedirects);
	}

	/**
	 * Check that a 200 HTTP Status is returned with the correct Location URL.
	 *
	 * @param string $url      Relative or absolute URL of redirect destination.
	 * @param string $protocol Protocol: 'http' or 'https'.
	 */
	protected function permanentRedirectForProtocol($url, $protocol)
	{
		$url = ltrim($url, '/');
		$this->getModule('REST')->sendHead($url);

		$client       = $this->getModule('PhpBrowser')->client;
		$responseCode = $client->getInternalResponse()->getStatus();
		$responseUri  = $client->getHistory()->current()->getUri();
		$scheme       = parse_url($responseUri, PHP_URL_SCHEME);

		// Check for 200 response code.
		$this->assertEquals(200, $responseCode);

		// Check for submitted http/https value matches destination URL.
		$this->assertEquals($protocol, $scheme);

		// Check that destination URL contains submitted URL part.
		$this->assertContains($url, $responseUri);
	}
}

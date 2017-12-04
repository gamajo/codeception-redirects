<?php
/**
 * Redirects module for Codeception.
 *
 * @package   gamajo\codeception-redirects
 * @author    Gary Jones
 * @copyright 2016 Gamajo
 * @license   MIT
 */

namespace Codeception\Module;

use Codeception\Module;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Response;

/**
 * Redirects module for Codeception.
 *
 * @package Codeception\Module
 * @author  Gary Jones
 */
class Redirects extends Module
{
    /**
     * Protocol (http) constant that can be used with permanentRedirectForProtocol() method.
     */
    const PROTOCOL_HTTP = 'http';

    /**
     * Protocol (https) constant that can be used with permanentRedirectForProtocol() method.
     */
    const PROTOCOL_HTTPS = 'https';

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
     * Check if redirections are being followed or not.
     *
     * @since 0.2.0
     *
     * @return bool True if redirects are being followed, false otherwise.
     */
    protected function isFollowingRedirects()
    {
        return $this->getModule('PhpBrowser')->client->isFollowingRedirects();
    }

	/**
	 * Check that a redirection occurs.
	 *
	 * @since 0.2.0
	 *
	 * @param string  $oldUrl     Relative or absolute URL that should be redirected.
	 * @param string  $newUrl     Relative or absolute URL of redirect destination.
	 * @param integer $statusCode Status code to check for.
	 */
	public function seeRedirectBetween($oldUrl, $newUrl, $statusCode)
	{
		// We must not follow all redirects, so save current situation,
		// force disable follow redirects, and revert at the end.
		$followsRedirects = $this->isFollowingRedirects();
		$this->followRedirects(false);

		$response = $this->sendHeadAndGetResponse($oldUrl);

		if (null !== $response) {
			$responseCode   = $response->getStatus();
			$locationHeader = $response->getHeader('Location', true);

			// Check for correct response code.
			$this->assertEquals($statusCode, $responseCode, 'Response code was not ' . $statusCode . '.');

			// Check location header URL contains submitted URL.
			$this->assertContains($newUrl, $locationHeader, 'Redirect destination not found in Location header.');
		}


		$this->followRedirects($followsRedirects);
	}

    /**
     * Convenience method to check that a 301 HTTP Status is returned with the correct Location URL.
     *
     * @since 0.2.0
     *
     * @param string $oldUrl Relative or absolute URL that should be redirected.
     * @param string $newUrl Relative or absolute URL of redirect destination.
     */
    public function seePermanentRedirectBetween($oldUrl, $newUrl)
    {
        $this->seeRedirectBetween($oldUrl, $newUrl, 301);
    }

    /**
     * Convenience method to check that a 307 HTTP Status is returned with the correct Location URL.
     *
     * @since 0.3.0
     *
     * @param string $oldUrl Relative or absolute URL that should be redirected.
     * @param string $newUrl Relative or absolute URL of redirect destination.
     */
    public function seeTemporaryRedirectBetween($oldUrl, $newUrl)
    {
        $this->seeRedirectBetween($oldUrl, $newUrl, 307);
    }

    /**
     * Check that a 200 HTTP Status is returned and the URL has no redirects.
     *
     * @since 0.1.3
     * @since 0.2.0 Renamed method, made public.
     *
     * @param string $url Relative or absolute URL of redirect destination.
     */
    public function urlDoesNotRedirect($url)
    {
        if ('/' === $url) {
            $url = '';
        }

        // We must not follow all redirects, so save current situation,
        // force disable follow redirects, and revert at the end.
        $followsRedirects = $this->isFollowingRedirects();
        $this->followRedirects(false);

        $response       = $this->sendHeadAndGetResponse($url);

        if (null !== $response) {
            $responseCode   = $response->getStatus();
            $locationHeader = $response->getHeader('Location', true);

            // Check for 200 response code.
            $this->assertEquals(200, $responseCode, 'Response code was not 200.');

            // Check that destination URL does not try to redirect.
            // Somewhat redundant, as this should never appear with a 200 HTTP Status code anyway.
            $this->assertNull($locationHeader, 'Location header was found when it should not exist.');
        }

        $this->followRedirects($followsRedirects);
    }

    /**
     * Use REST Module to send HEAD request and return the response.
     *
     * @since 0.2.0
     *
     * @param string $url
     *
     * @return null|Response
     */
    protected function sendHeadAndGetResponse($url)
    {
        /** @var REST $rest */
        $rest = $this->getModule('REST');
        $rest->sendHEAD($url);

        return $rest->client->getInternalResponse();
    }

    /**
     * Check that a 200 HTTP Status is eventually returned with the HTTP protocol.
     *
     * Should be HTTP. The URL should be the same - only the protocol is different.
     *
     * @since 0.1.0
     * @since 0.2.0 Method renamed.
     *
     * @param string $url Relative or absolute URL of redirect destination.
     */
    public function seeHttpProtocolAlwaysUsedFor($url)
    {
        $this->checkUrlAndProtocolAreCorrect($url, self::PROTOCOL_HTTP);
    }

    /**
     * Check that a 200 HTTP Status is eventually returned with the HTTPS protocol.
     *
     * Should be HTTPS. The URL should be the same - only the protocol is different.
     *
     * @since 0.1.0
     * @since 0.2.0 Method renamed.
     *
     * @param string $url Relative or absolute URL of redirect destination.
     */
    public function seeHttpsProtocolAlwaysUsedFor($url)
    {
        $this->checkUrlAndProtocolAreCorrect($url, self::PROTOCOL_HTTPS);
    }

    /**
     * For a given URL, check that a 200 HTTP Status is returned, the protocol matches given value,
     * and URL (except maybe protocol) has not changed.
     *
     * @since 0.1.0
     * @since 0.2.0 Method renamed.
     *
     * @param string $url      Relative or absolute URL of redirect destination.
     * @param string $protocol Protocol: 'http' or 'https'.
     */
    protected function checkUrlAndProtocolAreCorrect($url, $protocol)
    {
        // We must follow all redirects, so save current situation, force follow redirects, and revert at the end.
        $followsRedirects = $this->isFollowingRedirects();
        $this->followRedirects();

        $url = ltrim($url, '/');

        /** @var REST $rest */
        $rest = $this->getModule('REST');
        $rest->sendHEAD($url);

        /** @var Client $client */
        $client       = $this->getModule('PhpBrowser')->client;
        $responseCode = $client->getInternalResponse()->getStatus();
        $responseUri  = $client->getHistory()->current()->getUri();
        $scheme       = parse_url($responseUri, PHP_URL_SCHEME);

        // Check for 200 response code.
        $this->assertEquals(200, $responseCode, 'Response code was not 200.');

        // Check that destination URL contains submitted URL part.
        $this->assertContains($url, $responseUri, 'Destination URL does not contain the original URL.');

        // Check for submitted http/https value matches destination URL.
        $this->assertEquals($protocol, $scheme, 'Protocol at destination URL does not match expected value.');

        $this->followRedirects($followsRedirects);
    }
}

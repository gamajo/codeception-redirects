<?php
/**
 * Redirects module for Codeception.
 *
 * @package   gamajo\codeception-redirects
 * @author    Gary Jones
 * @copyright 2016 Gamajo Tech
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
     * Check that a 301 HTTP Status is returned with the correct Location URL.
     *
     * @since 0.1.0
     *
     * @param string $url Relative or absolute URL of redirect destination.
     * @param string $checkDestinationExists Optional. Whether to check if destination URL is 200 OK.
     */
    public function seePermanentRedirectTo($url, $checkDestinationExists = true )
    {
        $followsRedirects = $this->getModule('PhpBrowser')->client->isFollowingRedirects();

        /** @var Response $response */
        $response       = $this->getModule('PhpBrowser')->client->getInternalResponse();
        $responseCode   = $response->getStatus();
        $locationHeader = $response->getHeader('Location', true);

        // Check for 301 response code.
        $this->assertEquals(301, $responseCode);

        // Check location header URL contains submitted URL.
        $this->assertContains($url, $locationHeader);

        if ($checkDestinationExists) {
            $this->followRedirects( true );
            $this->urlExists( $url );
            $this->followRedirects( $followsRedirects );
        }
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
     * @since 0.1.0
     *
     * @param string $url      Relative or absolute URL of redirect destination.
     * @param string $protocol Protocol: 'http' or 'https'.
     */
    protected function permanentRedirectForProtocol($url, $protocol)
    {
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
        $this->assertEquals(200, $responseCode);

        // Check that destination URL contains submitted URL part.
        $this->assertContains($url, $responseUri);

        // Check for submitted http/https value matches destination URL.
        $this->assertEquals($protocol, $scheme);
    }

    /**
     * Check that a 200 HTTP Status is returned and the final URL has no more redirects.
     *
     * @since 0.1.3
     *
     * @param string $url      Relative or absolute URL of redirect destination.
     * @param string $protocol Protocol: 'http' or 'https'.
     */
    protected function urlExists($url)
    {
        $url = ltrim($url, '/');

        /** @var REST $rest */
        $rest = $this->getModule('REST');
        $rest->sendHEAD($url);
        $responseCode = $rest->client->getInternalResponse()->getStatus();
        $locationHeader = $rest->client->getInternalResponse()->getHeader('Location');

        // Check for 200 response code.
        $this->assertEquals(200, $responseCode);
        // Check that destination URL does not try to redirect.
        $this->assertNull($locationHeader);
    }
}

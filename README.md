# Codeception Redirects Module

Uses the REST module to check URLs for `Location` response headers and `301` HTTP Status response codes, or redirects between protocols.

Handy for checking that `.htaccess` or other methods of web page redirects have been set up correctly.

If test fails stores last shown page in `output` dir.

## Status

* Maintainer: **GaryJones**
* Stability: **alpha**
* Contact: https://gamajo.com

*Please review the code of non-stable modules and provide patches if you have issues.*

## Configuration

None, but ensure that the REST module is enabled and has a `url` set.

### Example (`acceptance.suite.yml`)

~~~yaml
modules:
    enabled:
        - Redirects
        - REST:
            url: 'http://localhost'
~~~

## Example Usage

### 301 Redirects

Here's a Cept which checks 301 redirects. We turn off automatic following of redirects in the Symfony Browserkit client, so that it doesn't just follow the redirects to the final destination.

```php
<?php
// @group redirects

$I = new AcceptanceTester($scenario);
$I->wantTo('check 301 redirects are working');

// For all redirects, a Location header is sent, so we stop the redirect
// and just check that header instead.

$I->followRedirects(false);

// Check example.com/twitter redirects to my Twitter profile
$I->sendHEAD('twitter');
$I->seePermanentRedirectTo('https://twitter.com/GaryJ');

// Check example.com/company.cfm redirects to example.com/about-us
$I->sendHEAD('company.cfm');
$I->seePermanentRedirectTo('company/about-us');

```

### Protocol Redirects

Here's a Cest which checks protocol redirects, to see if a URL is forced to be served as `http://` or `https://`.

```php
<?php

use Page\ProfileCalendar;
use Page\ProfileContactInformation;
use Page\ProfileMyProducts;
use Step\Acceptance\Login;

class ProtocolRedirectsCest
{
    /**
     * @group protocolRedirects
     */
    public function forceHttp(Login $I)
    {
        $I->wantTo('check forced redirects to HTTP are working.');
        $I->seePermanentRedirectToHttpFor(ProfileCalendar::$URL);
    }

    /**
     * @group protocolRedirects
     */
    public function forceHttps(Login $I)
    {
        $I->wantTo('check forced redirects to HTTPS are working.');
        $I->seePermanentRedirectToHttpsFor(ProfileContactInformation::$URL);
        $I->seePermanentRedirectToHttpsFor(ProfileMyProducts::$URL);
    }
}

```

## Public API

Methods expected to be used in Cepts and Cests.

### followRedirects

Sets whether to automatically follow redirects or not.

```php
$I->followRedirects(false);
```

* `param bool` $followRedirects Whether to follow automatic redirects or not. Default behaviour is true, so most times you'll want to pass in false for 301 redirects tests.'

### seePermanentRedirectTo

Checks for a `Location` response header and a `301` HTTP Status response code. Fails if either is missing, or `Location` header value does not match the `$url`.

```php
$I->sendHEAD('company/financial-strength-and-security.cfm');
$I->seePermanentRedirectTo('company/financial-security');
```

* `param` $url Absolute or relative (to REST config `url`) URL.

### seePermanentRedirectToHttpFor

 Check that a 200 HTTP Status is returned with the URL as HTTP.

 ```php
$I->seePermanentRedirectToHttpFor('insecure-page');
 ```

* `param` $url Absolute or relative (to REST config `url`) URL.

### seePermanentRedirectToHttpsFor

 Check that a 200 HTTP Status is returned with the URL as HTTPS.

 ```php
$I->seePermanentRedirectToHttpsFor('contact-us');
 ```

* `param` $url Absolute or relative (to REST config `url`) URL.

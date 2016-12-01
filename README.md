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

Here's a Cest which checks 301 redirects. We turn off automatic following of redirects in the Symfony Browserkit client, so that it doesn't just follow the redirects to the final destination.

```php
<?php

use Codeception\Example;

class RedirectsCest {
    /**
     * @var AcceptanceTester
     */
    protected $I;

    public function _before( AcceptanceTester $I ) {
        $this->I = $I;
    }

    /**
     * @example(old="content/abou", new="about-us")
     * @example(old="content/abou/over.php", new="about-us/company-overview")
     * @example(old="content/abou/miss.php", new="about-us/top-third-mission")
     * @example(old="content/abou/exec.php", new="about-us/executive-team")
     * @example(old="content/abou/team.php", new="about-us/risk-management-specialists")
     *
     * @group redirects
     * @group redirectsabout
     */
    public function redirectOldAboutUrlsToAboutUsPages( AcceptanceTester $I, Example $example ) {
        $this->testIfOldRedirectsToNew($example['old'], $example['new']);
    }

    /**
     * @example(old="content/myac/index.php", new="my-account")
     * @example(old="content/myac/stat.php", new="my-account/account-statements-explained")
     * @example(old="content/myac/depo.php", new="my-account/deposits-withdrawals")
     * @example(old="content/myac/wire.php", new="wire-instructions-r-j-obrien")
     *
     * @group redirects
     * @group redirectsmyaccount
     */
    public function redirectOldMyAccountUrlsToNewMyAccountPages( AcceptanceTester $I, Example $example ) {
        $this->testIfOldRedirectsToNew( $example['old'], $example['new'] );
    }

    private function testIfOldRedirectsToNew($old, $new, $checkDestination = true) {
        $this->I->seePermanentRedirectBetween($old, $new);
        if ($checkDestinationExists) {
            $this->I->urlDoesNotRedirect($new);
        }

        // Check old URL with trailing slash also redirects.
        if (
            '/' !== substr($old, -1) &&
            false === strpos( strrev($old), strrev('.php')) &&
            false === strpos( strrev($old), strrev('.pdf')) &&
            false === strpos( $old, '?')
        ) {
            $old .= '/';
            $this->testIfOldRedirectsToNew($old, $new, $checkDestinationExists);
        }
    }
}

```

Grouping the tests like these mean you can run individual groups of tests more easily:

```sh
vendor/bin/codecept run --env=staging --group=redirectsmyaccount
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
        $I->seeHttpProtocolAlwaysUsedFor(ProfileCalendar::$URL);
    }

    /**
     * @group protocolRedirects
     */
    public function forceHttps(Login $I)
    {
        $I->wantTo('check forced redirects to HTTPS are working.');
        $I->seeHttpsProtocolAlwaysUsedFor(ProfileContactInformation::$URL);
        $I->seeHttpsProtocolAlwaysUsedFor(ProfileMyProducts::$URL);
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

* `param bool` **`$followRedirects`**

    Whether to follow automatic redirects or not. Default behaviour is true, so most times you'll want to pass in false for 301 redirects tests. Other methods in this package already call this as needed.

### seePermanentRedirectBetween

Check that a 301 HTTP Status is returned with the correct Location URL. Fails if either is missing, or `Location` header value does not match the `$url`. Automatically avoids following redirects.

```php
$I->seePermanentRedirectBetween('company/financial-strength-and-security.cfm', 'company/financial-security');
```

* `param` **`$oldUrl`**

    Relative or absolute URL that should be redirected.
* `param` **`$newUrl`**

    Relative or absolute URL of redirect destination.

### urlDoesNotRedirect

 Check that a 200 HTTP Status is returned and the URL has no redirects. Allows the possibility of following redirects.

 ```php
$I->urlDoesNotRedirect('company/financial-security');
 ```

* `param` **`$url`**

    Absolute or relative (to REST config `url`) URL.

### seeHttpProtocolAlwaysUsedFor

 Check that a 200 HTTP Status is eventually returned with the HTTP protocol. Follows redirects automatically.

 ```php
$I->seePermanentRedirectToHttpFor('insecure-page');
 ```

* `param` **`$url`**

    Absolute or relative (to REST config `url`) URL.

### seePermanentRedirectToHttpsFor

 Check that a 200 HTTP Status is eventually returned with the HTTPS protocol. Follows redirects automatically.

 ```php
$I->seePermanentRedirectToHttpsFor('contact-us');
 ```

* `param` **`$url`**

    Absolute or relative (to REST config `url`) URL.

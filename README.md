EWZAuthBundle
=============

This bundle provides connection support for Facebook and Twitter for Symfony2.

## Installation

Installation depends on how your project is setup:

### Step 1: Installation using the `bin/vendors.php` method

If you're using the `bin/vendors.php` method to manage your vendor libraries,
add the following entries to the `deps` in the root of your project file:

```
[EWZTimeBundle]
    git=http://github.com/excelwebzone/EWZAuthBundle.git
    target=/bundles/EWZ/Bundle/EWZAuthBundle

; Dependencies:
;--------------
[facebook]
    git=http://github.com/facebook/php-sdk.git

[twitteroauth]
    git=http://github.com/ruudk/twitteroauth.git
```

Next, update your vendors by running:

``` bash
$ ./bin/vendors
```

Great! Now skip down to *Step 2*.

### Step 1 (alternative): Installation with submodules

If you're managing your vendor libraries with submodules, first create the
`vendor/bundles/EWZ/Bundle` directory:

``` bash
$ mkdir -pv vendor/bundles/EWZ/Bundle
```

Next, add the necessary submodules:

``` bash
$ git submodule add git://github.com/facebook/php-sdk.git vendor/facebook
$ git submodule add git://github.com/ruudk/twitteroauth.git vendor/twitteroauth
$ git submodule add git://github.com/excelwebzone/EWZAuthBundle.git vendor/bundles/EWZ/Bundle/EWZAuthBundle
```

### Step2: Configure the autoloader

Add the following entry to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...

    'TwitterOAuth' => __DIR__.'/../vendor/twitteroauth',
    'EWZ'          => __DIR__.'/../vendor/bundles',
));
```

### Step3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new EWZ\Bundle\AuthBundle\EWZAuthBundle(),
    );
}
```

### Step4: Configure the bundle's

Finally, add the following to your config file:

``` yaml
# app/config/config.yml

ewz_auth:
    facebook:
        app_id:    __APPID__
        secret:   __SECRET__

    twitter:
        key:      __KEY__
        secret:   __SECRET__
```

Congratulations! You're ready!

## Basic Usage

To retrieve the login url for a given the provider, use the following:

``` php
<?php

// load service
$service = $this->get('ewz_auth.facebook');

$loginUrl = $service->getLoginUrl(
    $this->generateUrl('ALLOW_URL', array('provider' => 'facebook'), true),
    $this->generateUrl('DENIED_URL', array('provider' => 'facebook', 'denied' => 't'), true),
    array(
        'display'   => 'popup',
        'req_perms' => 'email,offline_access',
    )
);

return new RedirectResponse($loginUrl);
```

Once return to the ALLOW_URL, we can then get all the profile information by using:

``` php
<?php

// load service
$service = $this->get('ewz_auth.facebook');

if (!$profile = $service->getProfile()) {
    return new Response('We couldn&#039;t connect you to Facebook at this time, please try again.');
}

// DO SOMETHING WITH $profile
...
```

In addition there is a way to retrieve the profile Friends list:

``` php
<?php

// load service
$service = $this->get('ewz_auth.facebook');

$friends = $service->getFriends($profile['id'], $profile['access_token']);
```

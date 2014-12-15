## reCAPTCHA Library

This is a package to implement reCAPTCHA (https://developers.google.com/recaptcha/)


## Installation

Require this package with composer:

```
composer require fruitcakestudio/recaptcha
```

## Configuration

You can create a new instance by passing the SiteKey and Secret from your API.
You can get that at https://www.google.com/recaptcha/admin

```php
use FruitcakeStudio\ReCaptcha\ReCaptcha;

$captcha = new ReCaptcha($siteKey, $secret, 'en');
```

## Widget usage

To show the reCAPTCHA on a form, use the class to render the script tag and the widget.

```php
<?php echo $captcha->getScript() ?>
<form method="POST">
    <?php echo $captcha->getWidget() ?>
    <input type="submit" value="Submit" />
</form>
```

See https://developers.google.com/recaptcha/docs/display for more options.


## Verifying a response

After the post, use the class to verify the response. You get true or false back.
You can access the error codes with $captcha->getErrors() as array, or get a readable message:

```php
if ($captcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"])) {
    echo "OK!";
} else {
    echo "FAILED! " . $captcha->getErrorMessage();
}
```

> Note: An error message is not always present.

You can also let the class discover the POST response and remote IP by using verifyGlobals();

```php
if ($captcha->verifyGlobals()) {
    echo "OK!";
}
```

If you are using Symfony HttpFoundation, you can use the Request object instead of the globals.

```php
if ($captcha->verifyRequest($request)) {
    echo "OK!";
}

// Or by setting the request before
$captcha->setRequest($request);
if ($captcha->verifyRequest()) {
    echo "OK!";
}
```

See the docs on https://developers.google.com/recaptcha/docs/verify

## Laravel integration

Since 0.3.x, Laravel integration is supported with a standalone package: https://github.com/fruitcakestudio/laravel-recaptcha

The classes are the same, only the configuration package name is 'laravel-recaptcha' instead of 'recaptcha'.


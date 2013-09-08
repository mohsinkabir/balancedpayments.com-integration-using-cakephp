balancedpayments.com-integration-using-cakephp
==============================================
Install Compser
================
If you don't have Composer install it:

If you don't have Composer install it:

$ curl -s https://getcomposer.org/installer | php

Add this to your composer.json:

{
    "require": {
        "balanced/balanced": "*"
    }
}

Refresh your dependencies:

$ php composer.phar update

Then make sure to require the autoloader and initialize all:
<?php
require(__DIR__ . '/vendor/autoload.php');

\Httpful\Bootstrap::init();
\RESTful\Bootstrap::init();
\Balanced\Bootstrap::init();
...


# Balanced Payments integration using cakephp #

## Install Composer ##

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

Then make sure to require the autoloader and initialize all. I added those codes on the component class file

    require(__DIR__ . '/vendor/autoload.php');
    
    \Httpful\Bootstrap::init();
    \RESTful\Bootstrap::init();
    \Balanced\Bootstrap::init();
    ...


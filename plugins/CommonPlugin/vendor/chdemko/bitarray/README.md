PHP BitArray
======================
[![Downloads](https://poser.pugx.org/chdemko/bitarray/d/total.png)](https://packagist.org/packages/chdemko/bitarray)
[![Latest Stable Version](https://poser.pugx.org/chdemko/bitarray/version.png)](https://packagist.org/packages/chdemko/bitarray)
[![Latest Unstable Version](https://poser.pugx.org/chdemko/bitarray/v/unstable.png)](https://packagist.org/packages/chdemko/bitarray)
[![Code coverage](https://coveralls.io/repos/chdemko/php-bitarray/badge.png?branch=master)](https://coveralls.io/r/chdemko/php-bitarray?branch=master)
[![Build Status](https://secure.travis-ci.org/chdemko/php-bitarray.png)](http://travis-ci.org/chdemko/php-bitarray)
[![License](https://poser.pugx.org/chdemko/bitarray/license.png)](http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html)

BitArray for PHP.

This project uses:

* [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer) for checking PHP code style using [Joomla Coding Standards](https://github.com/joomla/coding-standards)
* [PHPUnit](http://phpunit.de/) for unit test (100% covered)
* [phpDocumentor](http://http://www.phpdoc.org/) for api documentation

Installation
------------

Using composer: either

~~~
$ composer create-project chdemko/bitarray:1.0.x-dev --dev; cd bitarray
~~~

or create a `composer.json` file containing

~~~json
{
    "require": {
        "chdemko/bitarray": "1.0.x-dev"
    }
}
~~~
and run
~~~
$ composer install
~~~

Create a `test.php` file containg
~~~php
<?php
require __DIR__ . '/vendor/autoload.php';

use chdemko\BitArray\BitArray;

$bits = BitArray::fromIterable([true,false,false,true]);
echo $bits . PHP_EOL;
~~~
This should print
~~~
1001
~~~
See the [examples](https://github.com/chdemko/php-bitarray/tree/master/examples) folder for more information.

Documentation
-------------

* [http://chdemko.github.io/php-bitarray](http://chdemko.github.io/php-bitarray)

Citation
--------

If you are using this project including publication in research activities, you have to cite it using ([BibTeX format](https://raw.github.com/chdemko/php-bitarray/master/cite.bib)). You are also pleased to send me an email to chdemko@gmail.com.
* authors: Christophe Demko
* title: php-bitarray: a PHP library for handling bit arrays
* year: 2014
* how published: http://chdemko.github.io/php-bitarray


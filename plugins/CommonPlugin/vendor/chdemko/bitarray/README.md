PHP BitArray
======================
![PHP package](https://github.com/chdemko/php-bitarray/workflows/PHP%20Composer/badge.svg?branch=develop)
[![Documentation Status](https://img.shields.io/readthedocs/php-bitarray.svg)](http://php-bitarray.readthedocs.io/en/latest/?badge=latest)
[![Coveralls](https://img.shields.io/coveralls/chdemko/php-bitarray.svg)](https://coveralls.io/r/chdemko/php-bitarray?branch=develop)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/chdemko/php-bitarray/develop.svg)](https://scrutinizer-ci.com/g/chdemko/php-bitarray/?branch=develop)
[![PHP versions](https://img.shields.io/packagist/dependency-v/chdemko/bitarray/php)](https://packagist.org/packages/chdemko/bitarray)
[![Latest Stable Version](https://img.shields.io/packagist/v/chdemko/bitarray.svg)](https://packagist.org/packages/chdemko/bitarray)
[![Packagist](https://img.shields.io/packagist/dt/chdemko/bitarray.svg)](https://packagist.org/packages/chdemko/bitarray)
[![Latest Unstable Version](https://poser.pugx.org/chdemko/bitarray/v/unstable.svg)](https://packagist.org/packages/chdemko/bitarray)
[![License](https://poser.pugx.org/chdemko/bitarray/license.svg)](https://raw.githubusercontent.com/chdemko/php-bitarray/develop/LICENSE)

BitArray for PHP.

This project manipulates compact array of bit values stored internally as strings.

The bit arrays may have variable length specified when an object is created using either:

* a specific size;
* a traversable collection;
* a string representation of bits;
* a json representation of bits;
* a slice from another bit array;
* a concatenation from two others bit arrays.

The project provides methods to get and set bits values using PHP natural syntax as well as the iterator facility offered by the PHP `foreach` language construct.
It also provides methods for bitwise logical operations between two bit arrays `and`, `or`, `xor` and the `not` operation.

This project uses:

* [PHP Code Sniffer](https://github.com/squizlabs/php_codesniffer) for checking PHP code style
* [PHPUnit](http://phpunit.de/) for unit test (100% covered)
* [Sphinx](https://www.sphinx-doc.org/) and [Doxygen](https://www.doxygen.nl/) for the
  [documentation](http://php-sorted-collections.readthedocs.io/en/latest/?badge=latest)


Instructions
------------

Using composer: either

~~~shell
$ composer create-project chdemko/bitarray:1.2.x-dev --dev; cd bitarray
~~~

or create a `composer.json` file containing

~~~json
{
    "require": {
        "chdemko/bitarray": "1.2.x-dev"
    }
}
~~~

and run

~~~shell
$ composer install
~~~

Create a `test.php` file containing

~~~php
<?php

require __DIR__ . '/vendor/autoload.php';

use chdemko\BitArray\BitArray;

$bits = BitArray::fromTraversable([true,false,false,true]);
echo $bits . PHP_EOL;
~~~

This should print

~~~console
1001
~~~

See the [examples](https://github.com/chdemko/php-bitarray/tree/master/examples) folder for more information.

Documentation
-------------

Run

~~~shell
$ sudo apt install doxygen python3-pip python3-virtualenv
$ virtualenv venv
$ venv/bin/activate
(venv) $ pip install -r docs/requirements.txt
(venv) $ sphinx-build -b html docs/ html/
(venv) $ deactivate
$
~~~

if you want to create local documentation with Sphinx.

Citation
--------

If you are using this project including publication in research activities, you have to cite it using ([BibTeX format](https://raw.github.com/chdemko/php-bitarray/develop/cite.bib)). You are also pleased to send me an email to chdemko@gmail.com.
* authors: Christophe Demko
* title: php-bitarray: a PHP library for handling bit arrays
* year: 2014
* how published: https://packagist.org/packages/chdemko/bitarray/


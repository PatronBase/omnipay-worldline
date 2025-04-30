# Omnipay: Worldline

**Worldline driver for the Omnipay PHP payment processing library**

![Build Status](https://github.com/PatronBase/omnipay-worldline/actions/workflows/main.yml/badge.svg?branch=main)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/PatronBase/omnipay-worldline.svg?style=flat)](https://scrutinizer-ci.com/g/PatronBase/omnipay-worldline/code-structure)
[![Code Quality](https://img.shields.io/scrutinizer/g/PatronBase/omnipay-worldline.svg?style=flat)](https://scrutinizer-ci.com/g/PatronBase/omnipay-worldline/?branch=main)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/PatronBase/omnipay-worldline/version.png)](https://packagist.org/packages/patronbase/omnipay-worldline)
[![Total Downloads](https://poser.pugx.org/patronbase/omnipay-worldline/d/total.png)](https://packagist.org/packages/patronbase/omnipay-worldline)


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 7.2+. This package implements Worldline support for Omnipay. It includes
support for the hosted checkout (redirect, 3-party) version of the gateway via v2 of their API.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "PatronBase/omnipay-worldline": "~3.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Worldline

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/PatronBase/omnipay-worldline/issues),
or better yet, fork the library and submit a pull request.

Endroid QR Code
==============

*By [endroid](http://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![Build Status](http://img.shields.io/travis/endroid/QrCode.svg)](http://travis-ci.org/endroid/QrCode)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![License](http://img.shields.io/packagist/l/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![PayPayl donate button](http://img.shields.io/badge/paypal-donate-orange.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RGH86QN825TWN "Keep me off the streets")

This library helps you generate images containing a QR code.

```php
<?php

use Endroid\QrCode\QrCode;

$qrCode = new QrCode();
$qrCode
    ->setText("Life is too short to be generating QR codes")
    ->setSize(300)
    ->setPadding(10)
    ->setErrorCorrection('high')
    ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
    ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
    ->render()
;
```

![QR Code](http://endroid.nl/qrcode/Life%20is%20too%20short%20to%20be%20generating%20QR%20codes.png)

## Symfony

You can use [`EndroidQrCodeBundle`](https://github.com/endroid/EndroidQrCodeBundle) to integrate this service in your Symfony application.

## Versioning

Semantic versioning ([semver](http://semver.org/)) is applied.

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.

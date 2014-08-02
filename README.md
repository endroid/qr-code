Endroid QR Code
==============

*By [endroid](http://endroid.nl/)*

[![Build Status](https://secure.travis-ci.org/endroid/QrCode.png)](http://travis-ci.org/endroid/QrCode)
[![Latest Stable Version](https://poser.pugx.org/endroid/qrcode/v/stable.png)](https://packagist.org/packages/endroid/qrcode)
[![Total Downloads](https://poser.pugx.org/endroid/qrcode/downloads.png)](https://packagist.org/packages/endroid/qrcode)
[![Reference Status](https://www.versioneye.com/php/endroid:qrcode/reference_badge.svg?style=flat)](https://www.versioneye.com/php/endroid:qrcode/references)

This library helps you generate images containing a QR code.

```php
<?php

$qrCode = new Endroid\QrCode\QrCode();
$qrCode->setText("Life is too short to be generating QR codes");
$qrCode->setSize(300);
$qrCode->setPadding(10);
$qrCode->render();
```

![QR Code](http://endroid.nl/qrcode/Life_is_too_short_to_be_generating_QR_codes.png)

## Symfony

You can use [`EndroidQrCodeBundle`](https://github.com/endroid/EndroidQrCodeBundle) to enable this service in your Symfony application.

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.

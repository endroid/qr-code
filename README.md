Endroid QR Code
==============

*By [endroid](http://endroid.nl/)*

[![Build Status](https://secure.travis-ci.org/endroid/QrCode.png)](http://travis-ci.org/endroid/QrCode)

Tile helps you generate images containing a QR code.

```php
<?php

$qrCode = new Endroid\QrCode\QrCode();
$qrCode->setText("Life is too short to be generating QR codes");
$qrCode->setSize(300);
$qrCode->setPadding(10);
$qrCode->render();
```

## Symfony

You can use [`EndroidQrCodeBundle`](https://github.com/endroid/EndroidQrCodeBundle) to enable this service in your Symfony application.

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.

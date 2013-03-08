Endroid QR Code
==============

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

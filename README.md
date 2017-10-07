QR Code
=======

*By [endroid](http://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![Build Status](http://img.shields.io/travis/endroid/QrCode.svg)](http://travis-ci.org/endroid/QrCode)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![Monthly Downloads](http://img.shields.io/packagist/dm/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)
[![License](http://img.shields.io/packagist/l/endroid/qrcode.svg)](https://packagist.org/packages/endroid/qrcode)

This library helps you generate QR codes in an easy way and provides a Symfony
bundle for rapid integration in your project.

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/qrcode
```

## Basic usage

```php
use Endroid\QrCode\QrCode;

$qrCode = new QrCode('Life is too short to be generating QR codes');

header('Content-Type: '.$qrCode->getContentType());
echo $qrCode->writeString();
```

## Advanced usage

```php
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Response;

// Create a basic QR code
$qrCode = new QrCode('Life is too short to be generating QR codes');
$qrCode->setSize(300);

// Set advanced options
$qrCode
    ->setWriterByName('png')
    ->setMargin(10)
    ->setEncoding('UTF-8')
    ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
    ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
    ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
    ->setLabel('Scan the code', 16, __DIR__.'/../assets/noto_sans.otf', LabelAlignment::CENTER)
    ->setLogoPath(__DIR__.'/../assets/symfony.png')
    ->setLogoWidth(150)
    ->setValidateResult(false)
;

// Directly output the QR code
header('Content-Type: '.$qrCode->getContentType());
echo $qrCode->writeString();

// Save it to a file
$qrCode->writeFile(__DIR__.'/qrcode.png');

// Create a response object
$response = new Response($qrCode->writeString(), Response::HTTP_OK, ['Content-Type' => $qrCode->getContentType()]);
```

![QR Code](http://endroid.nl/qrcode/Dit%20is%20een%20test.png)

## Symfony integration

When you use Symfony Flex, the bundle is automatically registered and the
configuration and routes are automatically created when you installed the
package. In other scenarios you can register the bundle as follows.

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Endroid\QrCode\Bundle\EndroidQrCodeBundle(),
    ];
}
```

The bundle makes use of a factory to create QR codes. The default parameters
applied by the factory can optionally be overridden via the configuration.

```yaml
endroid_qr_code:
    writer: 'png'
    size: 300
    margin: 10
    foreground_color: { r: 0, g: 0, b: 0 }
    background_color: { r: 255, g: 255, b: 255 }
    error_correction_level: low # low, medium, quartile or high
    encoding: UTF-8
    label: Scan the code
    label_font_size: 20
    label_alignment: left # left, center or right
    label_margin: { b: 20 }
    logo_path: '%kernel.root_dir%/../vendor/endroid/qrcode/assets/symfony.png'
    logo_width: 150
    validate_result: false # checks if the result is readable
```

The readability of a QR code is primarily determined by the size, the input
length, the error correction level and any possible logo over the image. The
`validate_result` option uses a built-in reader to validate the resulting
image. This does not guarantee that the code will be readable by all readers
but this helps you provide a minimum level of quality. Take note that the
validator can consume quite an amount of resources and is disabled by default.

Now you can retrieve the factory from the service container and create a QR
code. For instance in your controller this would look like this.

```php
$qrCode = $this->get('endroid.qrcode.factory')->create('QR Code', ['size' => 200]);
```

Add the following section to your routing to be able to handle QR code URLs.
This step can be skipped if you only use data URIs to display your images.

``` yml
EndroidQrCodeBundle:
    resource: "@EndroidQrCodeBundle/Controller/"
    type:     annotation
    prefix:   /qrcode
```

After installation and configuration, QR codes can be generated by appending
the QR code text to the url followed by any of the supported extensions.

## Twig extension

The bundle provides a Twig extension for generating a QR code URL, path or data
URI. You can use the second argument of any of these functions to override any
defaults defined by the bundle or set via your configuration.

``` twig
<img src="{{ qrcode_path(message) }}" />
<img src="{{ qrcode_url(message, { writer: 'eps' }) }}" />
<img src="{{ qrcode_data_uri(message, { writer: 'svg', size: 150 }) }}" />
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatibility
breaking changes will be kept to a minimum but be aware that these can occur.
Lock your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.

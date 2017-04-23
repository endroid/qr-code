<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PngDataUriWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgDataUriWriter;
use Endroid\QrCode\Writer\SvgWriter;
use PHPUnit\Framework\TestCase;

class QrCodeTest extends TestCase
{
    public function testReadable()
    {
        $messages = [
            'Tiny',
            'This one has spaces',
            'http://this.is.an/url?with=query&string=attached',
            '{"i":"serialized.data","v":1,"t":1,"d":"4AEPc9XuIQ0OjsZoSRWp9DRWlN6UyDvuMlyOYy8XjOw="}',
            'Spëci&al ch@ract3rs',
        ];

        foreach ($messages as $message) {
            $qrCode = new QrCode($message);
            $qrCode->setValidateResult(true);
            $pngData = $qrCode->writeString(PngWriter::class);
            $this->assertTrue(is_string($pngData));
        }
    }

    public function testWriteQrCode()
    {
        $qrCode = new QrCode('QrCode');

        $binData = $qrCode->writeString(BinaryWriter::class);
        $this->assertTrue(is_string($binData));

        $epsData = $qrCode->writeString(EpsWriter::class);
        $this->assertTrue(is_string($epsData));

        $pngDataUriData = $qrCode->writeString(PngDataUriWriter::class);
        $this->assertTrue(strpos($pngDataUriData, 'data:image/png;base64') === 0);

        $pngData = $qrCode->writeString(PngWriter::class);
        $this->assertTrue(is_string($pngData));

        $svgDataUriData = $qrCode->writeString(SvgDataUriWriter::class);
        $this->assertTrue(strpos($svgDataUriData, 'data:image/svg+xml;base64') === 0);

        $svgData = $qrCode->writeString(SvgWriter::class);
        $this->assertTrue(is_string($svgData));
    }

    public function testSetSize()
    {
        $size = 400;

        $qrCode = new QrCode('QrCode');
        $qrCode->setSize($size);

        $pngData = $qrCode->writeString(PngWriter::class);
        $image = imagecreatefromstring($pngData);

        $this->assertTrue(imagesx($image) === $size);
        $this->assertTrue(imagesy($image) === $size);
    }

    public function testSetLabel()
    {
        $qrCode = new QrCode('QrCode');
        $qrCode
            ->setSize(300)
            ->setLabel('Scan the code', 15)
        ;

        $pngData = $qrCode->writeString(PngWriter::class);
        $this->assertTrue(is_string($pngData));
    }

    public function testSetLogo()
    {
        $qrCode = new QrCode('QrCode');
        $qrCode
            ->setSize(400)
            ->setLogoPath(__DIR__.'/../logo/symfony.png')
            ->setLogoSize(150)
            ->setValidateResult(true);
        ;

        $pngData = $qrCode->writeString(PngWriter::class);
        $this->assertTrue(is_string($pngData));
    }
}

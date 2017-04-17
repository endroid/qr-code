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
use Endroid\QrCode\Writer\DataUriWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use PHPUnit\Framework\TestCase;
use QrReader;

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

        $dataUriData = $qrCode->writeString(DataUriWriter::class);
        $this->assertTrue(strpos($dataUriData, 'data:image/png;base64') === 0);

        $epsData = $qrCode->writeString(EpsWriter::class);
        $this->assertTrue(is_string($epsData));

        $pngData = $qrCode->writeString(PngWriter::class);
        $this->assertTrue(is_string($pngData));

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
        $qrCode->setSize(300);
        $qrCode->setLabel('Scan the code', 60, QrCode::DEFAULT_FONT_PATH);

        $pngData = $qrCode->writeString(PngWriter::class);
    }
}

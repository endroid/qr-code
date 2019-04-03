<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Factory\QrCodeFactory;
use Endroid\QrCode\QrCode;
use PHPUnit\Framework\TestCase;
use Zxing\QrReader;

class QrCodeTest extends TestCase
{
    public function testReadable(): void
    {
        $messages = [
            'Tiny',
            'This one has spaces',
            'd2llMS9uU01BVmlvalM2YU9BUFBPTTdQMmJabHpqdndt',
            'http://this.is.an/url?with=query&string=attached',
            '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111',
            '{"i":"serialized.data","v":1,"t":1,"d":"4AEPc9XuIQ0OjsZoSRWp9DRWlN6UyDvuMlyOYy8XjOw="}',
            'Spëci&al ch@ract3rs',
            '有限公司',
        ];

        $qrCode = new QrCode();
        $qrCode->setSize(300);
        foreach ($messages as $message) {
            $qrCode->setText($message);
            $pngData = $qrCode->writeString();
            $this->assertTrue(is_string($pngData));
            $reader = new QrReader($pngData, QrReader::SOURCE_TYPE_BLOB);
            $this->assertEquals($message, $reader->text());
        }
    }

    public function testFactory(): void
    {
        $qrCodeFactory = new QrCodeFactory();
        $qrCode = $qrCodeFactory->create('QR Code', [
            'writer' => 'png',
            'size' => 300,
            'margin' => 10,
        ]);

        $pngData = $qrCode->writeString();
        $this->assertTrue(is_string($pngData));
        $reader = new QrReader($pngData, QrReader::SOURCE_TYPE_BLOB);
        $this->assertEquals('QR Code', $reader->text());
    }

    /**
     * @dataProvider writerNamesProvider
     */
    public function testWriteQrCodeByWriterName($writerName, $fileContent): void
    {
        $qrCode = new QrCode('QR Code');
        $qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
        $qrCode->setLogoWidth(100);

        $qrCode->setWriterByName($writerName);
        $data = $qrCode->writeString();
        $this->assertTrue(is_string($data));

        if (null !== $fileContent) {
            $uriData = $qrCode->writeDataUri();
            $this->assertTrue(0 === strpos($uriData, $fileContent));
        }
    }

    public function writerNamesProvider()
    {
        return [
            ['binary', null],
            ['debug', null],
            ['eps', null],
            ['png', 'data:image/png;base64'],
            ['svg', 'data:image/svg+xml;base64'],
        ];
    }

    /**
     * @dataProvider extensionsProvider
     */
    public function testWriteQrCodeByWriterExtension($extension, $fileContent): void
    {
        $qrCode = new QrCode('QR Code');
        $qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
        $qrCode->setLogoWidth(100);

        $qrCode->setWriterByExtension($extension);
        $data = $qrCode->writeString();
        $this->assertTrue(is_string($data));

        if (null !== $fileContent) {
            $uriData = $qrCode->writeDataUri();
            $this->assertTrue(0 === strpos($uriData, $fileContent));
        }
    }

    public function extensionsProvider()
    {
        return [
            ['bin', null],
            ['txt', null],
            ['eps', null],
            ['png', 'data:image/png;base64'],
            ['svg', 'data:image/svg+xml;base64'],
        ];
    }

    public function testSetSize(): void
    {
        $size = 400;
        $margin = 10;

        $qrCode = new QrCode('QR Code');
        $qrCode->setSize($size);
        $qrCode->setMargin($margin);

        $pngData = $qrCode->writeString();
        $image = imagecreatefromstring($pngData);

        $this->assertTrue(imagesx($image) === $size + 2 * $margin);
        $this->assertTrue(imagesy($image) === $size + 2 * $margin);
    }

    public function testSetLabel(): void
    {
        $qrCode = new QrCode('QR Code');
        $qrCode->setSize(300);
        $qrCode->setLabel('Scan the code', 15);

        $pngData = $qrCode->writeString();
        $this->assertTrue(is_string($pngData));
        $reader = new QrReader($pngData, QrReader::SOURCE_TYPE_BLOB);
        $this->assertEquals('QR Code', $reader->text());
    }

    public function testSetLogo(): void
    {
        $qrCode = new QrCode('QR Code');
        $qrCode->setSize(500);
        $qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
        $qrCode->setLogoWidth(100);
        $qrCode->setValidateResult(true);

        $pngData = $qrCode->writeString();
        $this->assertTrue(is_string($pngData));
    }

    public function testWriteFile(): void
    {
        $filename = __DIR__.'/output/qr-code.png';

        $qrCode = new QrCode('QR Code');
        $qrCode->writeFile($filename);

        $image = imagecreatefromstring(file_get_contents($filename));

        $this->assertTrue(is_resource($image));
    }

    public function testData(): void
    {
        $qrCode = new QrCode('QR Code');

        $data = $qrCode->getData();

        $this->assertArrayHasKey('block_count', $data);
        $this->assertArrayHasKey('block_size', $data);
        $this->assertArrayHasKey('inner_width', $data);
        $this->assertArrayHasKey('inner_height', $data);
        $this->assertArrayHasKey('outer_width', $data);
        $this->assertArrayHasKey('outer_height', $data);
        $this->assertArrayHasKey('margin_left', $data);
        $this->assertArrayHasKey('margin_right', $data);
    }
}

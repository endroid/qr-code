<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\QrCode;
use PHPUnit_Framework_TestCase;
use QrReader;

class QrCodeTest extends PHPUnit_Framework_TestCase
{
    public function testCreateQrCode()
    {
        $qrCode = new QrCode('QrCode');

        $this->assertTrue($qrCode instanceof QrCode);
    }

    public function testReadQrCodes()
    {
        $messages = [
            'Tiny',
            'This one has spaces',
            'http://this.is.an/url?with=query&string=attached',
        ];

        foreach ($messages as $message) {
            $this->readQrCode($message);
        }
    }

    protected function readQrCode($message)
    {
        $qrCode = new QrCode($message);

        $pngData = $qrCode->getPngData();
        $reader = new QrReader($pngData, QrReader::SOURCE_TYPE_BLOB);
        $text = $reader->text();

        $this->assertTrue($text === $message);
    }

    public function testSetSize()
    {
        $size = 400;
        
        $qrCode = new QrCode('QrCode');
        $qrCode->setSize($size);

        $pngData = $qrCode->getPngData();
        $image = imagecreatefromstring($pngData);

        $this->assertTrue(imagesx($image) === $size);
        $this->assertTrue(imagesy($image) === $size);
    }

    public function testGetPngDataUri()
    {
        $qrCode = new QrCode('QrCode');

        $dataUri = $qrCode->getPngDataUri();

        $this->assertTrue(strpos($dataUri,'data:image/png;base64') === 0);
    }


//
//    /**
//     * Tests if a valid image string is returned.
//     *
//     * @throws ImageFunctionFailedException
//     * @throws ImageFunctionUnknownException
//     */
//    public function testGetQrCodeWithLogoString()
//    {
//        $qrCode = $this->createQrCodeWithLogo();
//        $imageString = $qrCode->get('png');
//
//        $this->assertTrue(is_string($imageString));
//    }
//
//    /**
//     * For https://github.com/endroid/QrCode/issues/49.
//     */
//    public function testRenderHttpAddress()
//    {
//        $qrCode = new QrCode();
//        $qrCode
//            ->setText('http://www.example.com/it/it/contact/qr/hit/id/1  ')
//            ->setExtension('png')
//            ->setSize(300)
//            ->setPadding(10)
//            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
//            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
//            ->setErrorCorrection(QrCode::LEVEL_MEDIUM);
//
//        $qrCode->get('png');
//    }
//
//    /**
//     * Creates a QR code with a logo.
//     *
//     * @return QrCode
//     */
//    protected function createQrCodeWithLogo()
//    {
//        $qrCode = new QrCode();
//        $qrCode->setText('Life is too short to be generating QR codes')
//        ->setSize(300)
//        ->setLogo(dirname(__DIR__).'/assets/image/logo.png')
//        ->setLogoSize(60);
//
//        return $qrCode;
//    }
}

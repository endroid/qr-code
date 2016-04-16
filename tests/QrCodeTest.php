<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Tests\QrCode;

use Endroid\QrCode\Exceptions\ImageFunctionFailedException;
use Endroid\QrCode\Exceptions\ImageFunctionUnknownException;
use Endroid\QrCode\QrCode;
use PHPUnit_Framework_TestCase;

class QrCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var QrCode
     */
    protected $qrCode;

    /**
     * Tests if a valid data uri is returned.
     */
    public function testGetDataUri()
    {
        $qrCode = $this->createQrCode();
        $dataUri = $qrCode->getDataUri();

        $this->assertTrue(is_string($dataUri));
    }

    /**
     * Tests if a valid image string is returned.
     *
     * @throws ImageFunctionFailedException
     * @throws ImageFunctionUnknownException
     */
    public function testGetImageString()
    {
        $qrCode = $this->createQrCode();
        $imageString = $qrCode->get('png');

        $this->assertTrue(is_string($imageString));
    }

    /**
     * Tests if the resulting image size is correct.
     */
    public function testImageSize()
    {
        $qrCode = $this->createQrCode();

        $size = $qrCode->getSize();
        $padding = $qrCode->getPadding();
        $image = $qrCode->getImage();

        $this->assertTrue($padding > 0);
        $this->assertTrue(imagesx($image) == $size + 2 * $padding);
    }

    /**
     * Tests if a valid image string is returned.
     *
     * @throws ImageFunctionFailedException
     * @throws ImageFunctionUnknownException
     */
    public function testGetQrCodeWithLogoString()
    {
        $qrCode = $this->createQrCodeWithLogo();
        $imageString = $qrCode->get('png');

        $this->assertTrue(is_string($imageString));
    }

    /**
     * Creates a QR code.
     *
     * @return QrCode
     */
    protected function createQrCode()
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText('Life is too short to be generating QR codes')
            ->setSize(300)
            ->setPadding(20);

        return $qrCode;
    }

    /**
     * Creates a QR code with a logo.
     *
     * @return QrCode
     */
    protected function createQrCodeWithLogo()
    {
        $qrCode = $this->createQrCode();
        $qrCode
            ->setLogo(dirname(__DIR__).'/assets/image/logo.png')
            ->setLogoSize(60)
        ;

        return $qrCode;
    }
}

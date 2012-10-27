<?php

namespace Endroid\Tests\QrCode;

use Endroid\QrCode\QrCode;

class QrCodeTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateQrCode()
    {
        $qrCode = new QrCode();
        $qrCode->setText("Life is too short to be generating QR codes");
        $qrCode->setSize(300);
        $qrCode->create();

        $this->assertTrue(true);
    }
}

<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Label\Alignment\Center;
use Endroid\QrCode\Label\LabelBuilder;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Logo\LogoBuilder;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\High;
use Endroid\QrCode\QrCode\QrCodeBuilder;
use Endroid\QrCode\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\ResultInterface;

class QrCodeTest extends TestCase
{
    /**
     * @testdox The QR code can be created
     */
    public function testQrCode(): void
    {
        $result = Builder::create()
            ->data('data')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new High())
            ->writer(new PngWriter())
            ->logoPath(__DIR__.'/assets/symfony.png')
            ->logoResizeWidth(100)
            ->labelText('My fancy label')
            ->labelAlignment(new Center())
            ->labelMargin(new Margin(5, 5, 5, 5))
            ->build();

        $this->assertInstanceOf(ResultInterface::class, $result);
    }
}
